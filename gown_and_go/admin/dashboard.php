<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// =====================
// DASHBOARD STATS
// =====================

$total_customers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='customer'")->fetch_assoc()['c'];
$total_items = $conn->query("SELECT COUNT(*) AS c FROM items")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];

$total_revenue = $conn->query("
    SELECT COALESCE(SUM(amount),0) AS total 
    FROM payments 
    WHERE payment_status='Paid'
")->fetch_assoc()['total'];

// =====================
// RECENT ORDERS
// =====================

$recent_orders = $conn->query("
    SELECT o.order_id, o.order_date, o.order_status, o.total_amount, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
    LIMIT 5
");

// =====================
// INVENTORY
// =====================

$items = $conn->query("SELECT * FROM items ORDER BY created_at DESC");

// =====================
// RENTAL MONITORING (NEW)
// =====================

$rentals = $conn->query("
    SELECT 
        od.order_detail_id,
        o.order_id,
        i.name AS item_name,
        u.username,
        od.return_status
    FROM order_details od
    JOIN orders o ON od.order_id = o.order_id
    JOIN items i ON od.item_id = i.item_id
    JOIN users u ON o.user_id = u.user_id
    WHERE od.order_type = 'Rental'
    ORDER BY o.order_date DESC
");
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
h2.section-title {
    font-family: 'Playfair Display', serif;
    color: #d86ca1;
}
.btn-small {
    padding: 5px 10px;
    background: #d86ca1;
    color: white;
    border-radius: 6px;
    font-size: 0.8rem;
    text-decoration: none;
}
.btn-small:hover { background:#b3548a; }
.status-completed { color:green; font-weight:bold; }
.status-returned { color:green; font-weight:bold; }
.status-not-returned { color:red; font-weight:bold; }
</style>
</head>

<body>

<?php include 'inclusion/nav.php'; ?>

<div class="main-container">

<!-- ================= STATS ================= -->
<section class="grid">
<div class="card"><h3>Total Customers</h3><div class="value"><?= $total_customers ?></div></div>
<div class="card"><h3>Total Items</h3><div class="value"><?= $total_items ?></div></div>
<div class="card"><h3>Total Orders</h3><div class="value"><?= $total_orders ?></div></div>
<div class="card"><h3>Total Revenue</h3><div class="value">₱<?= number_format($total_revenue,2) ?></div></div>
</section>

<!-- ================= RECENT ORDERS ================= -->
<h2 class="section-title">Recent Orders</h2>
<table>
<tr><th>Order #</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th></tr>
<?php while($o = $recent_orders->fetch_assoc()): ?>
<tr>
<td>#<?= $o['order_id'] ?></td>
<td><?= $o['username'] ?></td>
<td><?= $o['order_date'] ?></td>
<td>
<?php if($o['order_status']=="Completed"): ?>
<span class="status-completed">Completed</span>
<?php else: ?>
<?= $o['order_status'] ?><br>
<a class="btn-small" href="complete_order.php?id=<?= $o['order_id'] ?>">Mark Completed</a>
<?php endif; ?>
</td>
<td>₱<?= number_format($o['total_amount'],2) ?></td>
</tr>
<?php endwhile; ?>
</table>

<!-- ================= RENTAL MONITORING (NEW) ================= -->
<h2 class="section-title mt-4">Rental Return Monitoring</h2>

<table>
<tr>
    <th>Order #</th>
    <th>Item</th>
    <th>Customer</th>
    <th>Return Status</th>
    <th>Action</th>
</tr>

<?php while($r = $rentals->fetch_assoc()): ?>
<tr>
<td>#<?= $r['order_id'] ?></td>
<td><?= htmlspecialchars($r['item_name']) ?></td>
<td><?= htmlspecialchars($r['username']) ?></td>
<td>

<?php if($r['return_status'] === "Returned"): ?>
    <span class="status-returned">Returned</span>
<?php else: ?>
    <span class="status-not-returned">Not Returned</span>
<?php endif; ?>


</td>
<td>
<?php if($r['return_status']=="Not Returned"): ?>
<a class="btn-small" href="mark_returned.php?id=<?= $r['order_detail_id'] ?>" 
onclick="return confirm('Mark this rental as returned?')">Mark Returned</a>
<?php else: ?>
—
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>

<!-- ================= INVENTORY ================= -->
<h2 class="section-title mt-4">Inventory Overview</h2>
<table>
<tr><th>Item</th><th>Stock</th><th>Purchase</th><th>Rental</th></tr>
<?php while($i=$items->fetch_assoc()): ?>
<tr>
<td><?= $i['name'] ?></td>
<td><?= $i['stock'] ?></td>
<td>₱<?= number_format($i['purchase_price'],2) ?></td>
<td>₱<?= number_format($i['rental_price'],2) ?></td>
</tr>
<?php endwhile; ?>
</table>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
