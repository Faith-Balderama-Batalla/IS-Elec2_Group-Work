<?php
session_start();
include 'config.php';

// Require login as customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // item_id => quantity
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id  = (int)$_POST['item_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    if (!isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] = 0;
    }
    $_SESSION['cart'][$item_id] += $quantity;

    $message = "Item added to cart!";
}

// Fetch available items
$items = [];
$result = $conn->query("SELECT * FROM items WHERE status = 'Available' AND stock > 0 ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Home - GOWN&GO</title>
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
      max-width: 1100px;
      margin: 30px auto 50px;
      padding: 20px;
      background: rgba(255,255,255,0.92);
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(183, 134, 154, 0.3);
    }
    .welcome {
      margin-bottom: 10px;
      font-size: 1.2rem;
    }
    .subtitle {
      margin-top: 0;
      margin-bottom: 25px;
      color: #666;
      font-size: 0.95rem;
    }
    .message {
      background: #e6ffe6;
      border: 1px solid #a3d7a3;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.9rem;
      color: #3c763d;
    }
    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 18px;
    }
    .item-card {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      display: flex;
      flex-direction: column;
    }
    .item-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: #f5f5f5;
    }
    .item-body {
      padding: 12px 14px 14px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .item-name {
      font-weight: 700;
      font-size: 1.05rem;
      margin-bottom: 4px;
      color: #6b2b4a;
    }
    .item-desc {
      font-size: 0.85rem;
      color: #777;
      margin-bottom: 8px;
      min-height: 40px;
    }
    .item-price {
      font-size: 0.9rem;
      margin-bottom: 8px;
    }
    .item-price strong {
      color: #d86ca1;
    }
    .item-stock {
      font-size: 0.8rem;
      color: #999;
      margin-bottom: 8px;
    }
    .item-form {
      margin-top: auto;
      display: flex;
      gap: 8px;
      align-items: center;
    }
    .item-form input[type="number"] {
      width: 60px;
      padding: 6px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-size: 0.9rem;
    }
    .btn {
      display: inline-block;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      border: none;
      cursor: pointer;
    }
    .btn-primary {
      background-color: #d86ca1;
      color: #fff;
    }
    .btn-primary:hover {
      background-color: #b3548a;
    }
    @media (max-width: 768px) {
      .topbar { padding: 10px 18px; }
      .main-container { margin: 20px; }
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
    <h2 class="welcome">Browse our collection</h2>
    <p class="subtitle">Rent or purchase elegant gowns and formal wear on the go.</p>

    <?php if (!empty($message)): ?>
      <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
      <p>No items available at the moment.</p>
    <?php else: ?>
      <section class="items-grid">
        <?php foreach ($items as $item): ?>
          <article class="item-card">
            <?php if (!empty($item['image'])): ?>
              <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Item image" class="item-image">
            <?php else: ?>
              <div class="item-image"></div>
            <?php endif; ?>
            <div class="item-body">
              <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
              <div class="item-desc"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
              <div class="item-price">
                <div>Purchase: <strong>₱<?php echo number_format($item['purchase_price'], 2); ?></strong></div>
                <div>Rental: <strong>₱<?php echo number_format($item['rental_price'], 2); ?></strong></div>
              </div>
              <div class="item-stock">In stock: <?php echo (int)$item['stock']; ?></div>
              <form method="POST" class="item-form">
                <input type="hidden" name="item_id" value="<?php echo (int)$item['item_id']; ?>">
                <input type="number" name="quantity" min="1" max="<?php echo (int)$item['stock']; ?>" value="1">
                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
