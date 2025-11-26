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

// Fetch customer info
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_res = $stmt->get_result();
$user = $user_res->fetch_assoc();

// Prepare cart data
$cart = $_SESSION['cart'];
$items_data = [];
$total = 0;

$ids = implode(',', array_map('intval', array_keys($cart)));
$result = $conn->query("SELECT * FROM items WHERE item_id IN ($ids)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items_data[$row['item_id']] = $row;
    }
}

foreach ($cart as $item_id => $qty) {
    if (isset($items_data[$item_id])) {
        $total += $items_data[$item_id]['purchase_price'] * $qty;
    }
}

$order_error = "";
$order_success = "";

// Handle placing order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $delivery_address = trim($_POST['delivery_address']);
    if ($delivery_address === "") {
        $order_error = "Delivery address is required.";
    } else {
        $conn->begin_transaction();
        try {
            // Insert into orders
            $stmt_order = $conn->prepare("
                INSERT INTO orders (user_id, order_status, order_type, total_amount, delivery_address)
                VALUES (?, 'Pending', 'Purchase', ?, ?)
            ");
            $stmt_order->bind_param("ids", $user_id, $total, $delivery_address);
            $stmt_order->execute();
            $order_id = $stmt_order->insert_id;

            // Insert order details
            $stmt_detail = $conn->prepare("
                INSERT INTO order_details (order_id, item_id, order_type, quantity, rental_period_days, unit_price, subtotal)
                VALUES (?, ?, 'Purchase', ?, NULL, ?, ?)
            ");

            foreach ($cart as $item_id => $qty) {
                if (!isset($items_data[$item_id])) continue;
                $price = $items_data[$item_id]['purchase_price'];
                $subtotal = $price * $qty;

                $stmt_detail->bind_param("iiidd", $order_id, $item_id, $qty, $price, $subtotal);
                $stmt_detail->execute();

                // Reduce stock
                $stmt_stock = $conn->prepare("UPDATE items SET stock = stock - ? WHERE item_id = ?");
                $stmt_stock->bind_param("ii", $qty, $item_id);
                $stmt_stock->execute();
            }

            // Insert payment record
            $stmt_pay = $conn->prepare("
                INSERT INTO payments (order_id, payment_method, payment_status, amount)
                VALUES (?, 'Cash on Delivery', 'Pending', ?)
            ");
            $stmt_pay->bind_param("id", $order_id, $total);
            $stmt_pay->execute();

            $conn->commit();

            $_SESSION['cart'] = [];

            // OPTION C — Success message + Link + Auto Redirect
            $order_success = "
                Order placed successfully!<br><br>
                <a href='invoice.php?order_id={$order_id}' 
                   style='color:#d86ca1;font-weight:bold;'>
                    View Invoice / Receipt
                </a>
                <script>
                    setTimeout(function(){
                        window.location.href = 'invoice.php?order_id={$order_id}';
                    }, 3000);
                </script>
            ";

        } catch (Exception $e) {
            $conn->rollback();
            $order_error = "Failed to place order. Please try again.";
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
    * { box-sizing: border-box; }
    body, html {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #6b2b4a;
    }
    a { color: #d86ca1; text-decoration: none; }
    a:hover { text-decoration: underline; }

    .topbar {
      background: rgba(255,255,255,0.9);
      padding: 12px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: #d86ca1;
    }
    .nav-links a {
      margin-left: 18px;
      font-size: 0.95rem;
    }
    .main-container {
      max-width: 900px;
      margin: 30px auto 50px;
      padding: 20px;
      background: rgba(255,255,255,0.92);
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(183, 134, 154, 0.3);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
      margin-bottom: 20px;
    }
    th, td {
      padding: 8px 10px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #f9e6f1;
    }
    .total-row td {
      font-weight: 700;
    }
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ddd;
      resize: vertical;
      margin-bottom: 15px;
    }
    .message {
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.9rem;
    }
    .error-message {
      background: #fbe3e4;
      border: 1px solid #f5c6cb;
      color: #a94442;
    }
    .success-message {
      background: #e6ffe6;
      border: 1px solid #a3d7a3;
      color: #3c763d;
    }
    .btn {
      display: inline-block;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      border: none;
      cursor: pointer;
      margin-right: 6px;
    }
    .btn-primary {
      background-color: #d86ca1;
      color: #fff;
    }
    .btn-primary:hover {
      background-color: #b3548a;
    }
    .btn-secondary {
      background-color: #eee;
      color: #555;
    }
  </style>
</head>
<body>
  <header class="topbar">
    <div class="logo">GOWN&GO</div>
    <div class="nav-links">
      <span>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
      <a href="client_home.php">Shop</a>
      <a href="cart.php">Cart</a>
      <a href="orders.php">My Orders</a>
      <a href="logout.php">Logout</a>
    </div>
  </header>

  <main class="main-container">
    <h2>Order Summary</h2>

    <?php if (!empty($order_error)): ?>
      <div class="message error-message"><?php echo $order_error; ?></div>
    <?php endif; ?>
    <?php if (!empty($order_success)): ?>
      <div class="message success-message"><?php echo $order_success; ?></div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th>Price (₱)</th>
          <th>Qty</th>
          <th>Subtotal (₱)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $item_id => $qty): ?>
          <?php if (!isset($items_data[$item_id])) continue; ?>
          <?php $item = $items_data[$item_id]; ?>
          <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo number_format($item['purchase_price'], 2); ?></td>
            <td><?php echo (int)$qty; ?></td>
            <td><?php echo number_format($item['purchase_price'] * $qty, 2); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="total-row">
          <td colspan="3" style="text-align:right;">Total:</td>
          <td>₱<?php echo number_format($total, 2); ?></td>
        </tr>
      </tbody>
    </table>

    <h3>Delivery Details</h3>
    <form method="POST">
      <textarea name="delivery_address" rows="3"><?php
        echo htmlspecialchars($user['address'] ?? '');
      ?></textarea>
      <br>
      <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
      <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
    </form>
  </main>
</body>
</html>
