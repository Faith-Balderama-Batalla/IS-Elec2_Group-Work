<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

// Remove item
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

// Fetch item info
$items_data = [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(",", array_map("intval", array_keys($cart)));
    $query = $conn->query("SELECT * FROM items WHERE item_id IN ($ids)");
    while ($row = $query->fetch_assoc()) {
        $items_data[$row['item_id']] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart - GOWN&GO</title>

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
    .topbar a {
        margin-left: 15px;
        color: #6b2b4a;
        text-decoration: none;
        font-weight: 600;
    }

    .main-container {
        max-width: 900px;
        margin: 40px auto;
        background: rgba(255,255,255,0.92);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(183,134,154,0.4);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
        margin-bottom: 20px;
    }
    th {
        background: #f9e6f1;
        padding: 10px;
        text-align: left;
    }
    td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .btn {
        display: inline-block;
        padding: 8px 12px;
        background: #d86ca1;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
    }
    .btn:hover {
        background: #b3548a;
    }
    .remove-link {
        color: #b3548a;
        text-decoration: none;
        font-weight: bold;
    }
    .remove-link:hover {
        color: #802f63;
    }
</style>
</head>

<body>

<header class="topbar">
    <div class="logo">GOWN&GO</div>
    <div>
        <a href="client_home.php">Shop</a>
        <a href="orders.php">Orders</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="main-container">
    <h2>My Cart</h2>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty.</p>

    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Price (₱)</th>
                    <th>Qty</th>
                    <th>Subtotal (₱)</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($cart as $item_id => $data): 
                if (!isset($items_data[$item_id])) continue;

                $item = $items_data[$item_id];
                $qty = $data['qty'];
                $type = $data['type'];

                // Determine correct price
                $price = ($type === "Rental")
                    ? $item['rental_price']
                    : $item['purchase_price'];

                $subtotal = $price * $qty;
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $type; ?></td>
                    <td><?php echo number_format($price, 2); ?></td>
                    <td><?php echo $qty; ?></td>
                    <td><?php echo number_format($subtotal, 2); ?></td>
                    <td><a class="remove-link" href="cart.php?remove=<?php echo $item_id; ?>">Remove</a></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="4" align="right"><strong>Total:</strong></td>
                <td><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                <td></td>
            </tr>
            </tbody>
        </table>

        <a href="checkout.php" class="btn">Proceed to Checkout</a>

    <?php endif; ?>
</div>

</body>
</html>
