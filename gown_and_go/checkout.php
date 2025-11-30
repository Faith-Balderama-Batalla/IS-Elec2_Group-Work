<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch all items in cart
$items_data = [];
$ids = implode(",", array_map("intval", array_keys($cart)));
$q = $conn->query("SELECT * FROM items WHERE item_id IN ($ids)");

while ($row = $q->fetch_assoc()) {
    $items_data[$row['item_id']] = $row;
}

// Compute total
$total = 0;

foreach ($cart as $item_id => $data) {
    $qty = $data['qty'];
    $type = $data['type'];

    $price = ($type === "Rental")
        ? $items_data[$item_id]['rental_price']
        : $items_data[$item_id]['purchase_price'];

    $total += $price * $qty;
}

$error = "";
$success = "";

// Place Order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {

    $delivery_address = trim($_POST['delivery_address']);

    if ($delivery_address === "") {
        $error = "Delivery address is required.";
    } else {

        $conn->begin_transaction();
        try {
            // Orders with mix of rental/purchase = Mixed
            $order_type = "Mixed";

            $stmt = $conn->prepare("
                INSERT INTO orders (user_id, order_status, order_type, total_amount, delivery_address)
                VALUES (?, 'Pending', ?, ?, ?)
            ");
            $stmt->bind_param("isds", $user_id, $order_type, $total, $delivery_address);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            // Insert order details
            $stmt_details = $conn->prepare("
                INSERT INTO order_details 
                    (order_id, item_id, order_type, quantity, rental_period_days, unit_price, subtotal)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($cart as $item_id => $data) {
                $qty = $data['qty'];
                $type = $data['type'];

                $item = $items_data[$item_id];

                if ($type === "Rental") {
                    $price = $item['rental_price'];
                    $days = 5;
                } else {
                    $price = $item['purchase_price'];
                    $days = 0;
                }

                $subtotal = $price * $qty;

                $stmt_details->bind_param(
                    "iisiddi",
                    $order_id,
                    $item_id,
                    $type,
                    $qty,
                    $days,
                    $price,
                    $subtotal
                );
                $stmt_details->execute();

                // Update stock
                $stmt_stock = $conn->prepare("UPDATE items SET stock = stock - ? WHERE item_id = ?");
                $stmt_stock->bind_param("ii", $qty, $item_id);
                $stmt_stock->execute();
            }

            // Payment record
            $stmt_pay = $conn->prepare("
                INSERT INTO payments (order_id, payment_method, payment_status, amount)
                VALUES (?, 'Cash on Delivery', 'Pending', ?)
            ");
            $stmt_pay->bind_param("id", $order_id, $total);
            $stmt_pay->execute();

            $conn->commit();

            $_SESSION['cart'] = [];

            $success = "
                <strong>Your order was placed successfully!</strong><br>
                Redirecting to invoice...
                <script>
                setTimeout(function(){
                    window.location.href='invoice.php?order_id=$order_id';
                }, 2000);
                </script>
            ";

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - GOWN&GO</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #6b2b4a;
    }
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(245,230,240,0.35);
        z-index: -1;
    }

    .topbar {
        background: rgba(255,255,255,0.9);
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .logo {
        font-family: 'Playfair Display', serif;
        font-size: 1.7rem;
        font-weight: 700;
        color: #d86ca1;
    }

    .main-box {
        max-width: 850px;
        margin: 40px auto;
        background: rgba(255,255,255,0.92);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(183,134,154,0.4);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }
    th {
        background: #f9e6f1;
        padding: 10px;
    }
    td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .msg-error {
        background: #ffe0e0;
        color: #b30000;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
    }
    .msg-success {
        background: #e8ffe8;
        color: #2d8a3d;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
    }

    .btn {
        background: #d86ca1;
        padding: 10px 16px;
        color: white;
        border-radius: 10px;
        border: none;
        font-weight: bold;
        cursor: pointer;
    }
    .btn:hover { background: #b3548a; }

    textarea,select {
      width: 100%;
      padding: 8px;
      margin-top: 4px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 0.95rem;
    }
    textarea {
      resize: vertical;
      min-height: 80px;
    }
</style>
</head>

<body>

<div class="main-box">

<h2>Checkout Summary</h2>

<?php if (!empty($error)): ?>
    <div class="msg-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="msg-success"><?php echo $success; ?></div>
<?php endif; ?>

<table>
<tr>
    <th>Item</th>
    <th>Type</th>
    <th>Qty</th>
    <th>Price (₱)</th>
    <th>Subtotal (₱)</th>
</tr>

<?php
foreach ($cart as $item_id => $data):
    $item = $items_data[$item_id];
    $qty = $data['qty'];
    $type = $data['type'];

    $price = ($type === "Rental")
        ? $item['rental_price']
        : $item['purchase_price'];

    $subtotal = $price * $qty;
?>
<tr>
    <td><?php echo htmlspecialchars($item['name']); ?></td>
    <td><?php echo $type; ?></td>
    <td><?php echo $qty; ?></td>
    <td><?php echo number_format($price,2); ?></td>
    <td><?php echo number_format($subtotal,2); ?></td>
</tr>
<?php endforeach; ?>

<tr>
    <td colspan="4" align="right"><strong>Total:</strong></td>
    <td><strong>₱<?php echo number_format($total,2); ?></strong></td>
</tr>

</table>

<form method="POST">
    <label><strong>Delivery Address:</strong></label>
    <textarea name="delivery_address" required></textarea>

    <br><br>
    <button type="submit" name="place_order" class="btn">Place Order</button>
    <button type="button" class="btn" style="background:#aaa;" onclick="window.location.href='cart.php'">Cancel</button>
</form>

</div>

</body>
</html>
