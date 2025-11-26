<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        o.order_id,
        o.order_date,
        o.order_status,
        o.total_amount,
        GROUP_CONCAT(CONCAT(i.name, ' (x', od.quantity, ')') SEPARATOR ', ') AS items
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN items i ON od.item_id = i.item_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$orders = [];
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - GOWN&GO</title>
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
    .status {
      text-transform: capitalize;
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
<h2>My Orders</h2>

<?php if (empty($orders)): ?>
    <p>You have no orders yet.</p>
<?php else: ?>

<table>
<thead>
<tr>
    <th>Order #</th>
    <th>Date</th>
    <th>Status</th>
    <th>Items</th>
    <th>Total (₱)</th>
    <th>Invoice</th>
</tr>
</thead>

<tbody>
<?php foreach ($orders as $o): ?>
<tr>
    <td>#<?php echo $o['order_id']; ?></td>
    <td><?php echo $o['order_date']; ?></td>
    <td><?php echo $o['order_status']; ?></td>
    <td><?php echo $o['items']; ?></td>
    <td><?php echo number_format($o['total_amount'], 2); ?></td>
    <td><a href="invoice.php?order_id=<?php echo $o['order_id']; ?>">View</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php endif; ?>
</main>

</body>
</html>