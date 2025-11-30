<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Stats
$total_customers = 0;
$total_items = 0;
$total_orders = 0;
$total_revenue = 0.00;

// Total customers
$res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'customer'");
if ($res && $row = $res->fetch_assoc()) {
    $total_customers = $row['c'];
}

// Total items
$res = $conn->query("SELECT COUNT(*) AS c FROM items");
if ($res && $row = $res->fetch_assoc()) {
    $total_items = $row['c'];
}

// Total orders
$res = $conn->query("SELECT COUNT(*) AS c FROM orders");
if ($res && $row = $res->fetch_assoc()) {
    $total_orders = $row['c'];
}

// Total revenue (paid payments only)
$res = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE payment_status = 'Paid'");
if ($res && $row = $res->fetch_assoc()) {
    $total_revenue = $row['total'];
}

// Recent orders
$recent_orders = [];
$sql = "
    SELECT o.order_id, o.order_date, o.order_status, o.total_amount, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
    LIMIT 5
";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}

// Inventory list
$items = [];
$res = $conn->query("SELECT * FROM items ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - GOWN&GO</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="inclusion/stylesheet.css">

<style>
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .card h3 {
      margin: 0 0 4px;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #999;
    }
    .card .value {
      font-size: 1.6rem;
      font-weight: 700;
      color: #6b2b4a;
    }
    .card .sub {
      font-size: 0.8rem;
      color: #aaa;
    }

    h2.section-title {
      margin-top: 0;
      margin-bottom: 10px;
      font-size: 1.2rem;
      color: #d86ca1;
      font-family: 'Playfair Display', serif;
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
    th { background: #f9e6f1; }

    .btn-small {
      padding: 5px 10px;
      background: #d86ca1;
      color: white;
      border-radius: 6px;
      font-size: 0.8rem;
      text-decoration: none;
      display: inline-block;
      margin-top: 6px;
    }
    .btn-small:hover {
      background: #b3548a;
    }

    .status-completed {
      font-weight: bold;
      color: green;
    }
</style>
</head>

<body>

<?php include 'inclusion/nav.php'; ?>

<div class="main-container">

<?php if (isset($_GET['success'])): ?>
    <div style="padding:10px; background:#e8ffe8; color:#1e7a1e; border-radius:8px; margin-bottom:15px;">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div style="padding:10px; background:#ffe8e8; color:#a40000; border-radius:8px; margin-bottom:15px;">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<section class="grid">
    <div class="card">
        <h3>Total Customers</h3>
        <div class="value"><?php echo (int)$total_customers; ?></div>
        <div class="sub">Registered users</div>
    </div>

    <div class="card">
        <h3>Total Items</h3>
        <div class="value"><?php echo (int)$total_items; ?></div>
        <div class="sub">In catalog</div>
    </div>

    <div class="card">
        <h3>Total Orders</h3>
        <div class="value"><?php echo (int)$total_orders; ?></div>
        <div class="sub">All time</div>
    </div>

    <div class="card">
        <h3>Total Revenue</h3>
        <div class="value">₱<?php echo number_format($total_revenue, 2); ?></div>
        <div class="sub">Paid orders</div>
    </div>
</section>

<section>
    <h2 class="section-title">Recent Orders</h2>

    <?php if (empty($recent_orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total (₱)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $o): ?>
            <tr>
                <td>#<?php echo (int)$o['order_id']; ?></td>
                <td><?php echo htmlspecialchars($o['username']); ?></td>
                <td><?php echo htmlspecialchars($o['order_date']); ?></td>
                <td>
                    <?php if ($o['order_status'] === "Completed"): ?>
                        <span class="status-completed">Completed</span>
                    <?php else: ?>
                        <?php echo htmlspecialchars($o['order_status']); ?>
                        <br>
                        <a class="btn-small" href="complete_order.php?id=<?php echo $o['order_id']; ?>">
                            Mark as Completed
                        </a>
                    <?php endif; ?>
                </td>
                <td><?php echo number_format($o['total_amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section>
    <h2 class="section-title">Inventory Overview</h2>

    <?php if (empty($items)): ?>
        <p>No items in inventory.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Status</th>
                <th>Stock</th>
                <th>Purchase Price (₱)</th>
                <th>Rental Price (₱)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i): ?>
            <tr>
                <td><?php echo htmlspecialchars($i['name']); ?></td>
                <td><?php echo htmlspecialchars($i['status']); ?></td>
                <td>
                    <?php echo (int)$i['stock']; ?>
                    <?php if ($i['stock'] <= 2): ?>
                        <span class="badge-low">Low</span>
                    <?php endif; ?>
                </td>
                <td><?php echo number_format($i['purchase_price'], 2); ?></td>
                <td><?php echo number_format($i['rental_price'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</div>
</body>
</html>
