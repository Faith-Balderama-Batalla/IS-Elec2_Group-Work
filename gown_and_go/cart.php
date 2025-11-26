<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Update quantities / remove items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $item_id => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$item_id]);
            } else {
                $_SESSION['cart'][$item_id] = $qty;
            }
        }
    }

    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }
}

$cart = $_SESSION['cart'];
$items_data = [];
$total = 0;

if (!empty($cart)) {
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
}

$cart_count = array_sum($cart);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart - GOWN&GO</title>
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
      <a href="cart.php">Cart (<?php echo $cart_count; ?>)</a>
      <a href="orders.php">My Orders</a>
      <a href="logout.php">Logout</a>
    </div>
  </header>

  <main class="main-container">
    <h2>Your Cart</h2>

    <?php if (empty($cart)): ?>
      <p>Your cart is empty. <a href="client_home.php">Browse items</a>.</p>
    <?php else: ?>
      <form method="POST">
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
                <td>
                  <input type="number" name="qty[<?php echo $item_id; ?>]" min="0" value="<?php echo (int)$qty; ?>" style="width:60px;">
                </td>
                <td><?php echo number_format($item['purchase_price'] * $qty, 2); ?></td>
              </tr>
            <?php endforeach; ?>
            <tr class="total-row">
              <td colspan="3" style="text-align:right;">Total:</td>
              <td>₱<?php echo number_format($total, 2); ?></td>
            </tr>
          </tbody>
        </table>

        <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
        <button type="submit" name="clear_cart" class="btn btn-secondary">Clear Cart</button>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
      </form>
    <?php endif; ?>
  </main>
</body>
</html>
