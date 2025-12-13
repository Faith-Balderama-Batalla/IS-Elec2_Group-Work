<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH USER DETAILS
========================= */
$stmt = $conn->prepare("
    SELECT username, email, address, contact_no 
    FROM users 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* =========================
   FETCH RECENT ORDERS

$orders = $conn->query("
    SELECT o.order_id, o.order_date, o.order_status, o.total_amount,
           od.order_type, od.return_status,
           p.payment_status
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
    LIMIT 5
");
========================= */
$orders = $conn->query("
    SELECT 
        o.order_id,
        MAX(od.order_type) AS order_type,
        MAX(od.return_status) AS return_status,
        o.order_status,
        o.total_amount,
        o.order_date,
        GROUP_CONCAT(i.name SEPARATOR ', ') AS item_names
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN items i ON od.item_id = i.item_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    WHERE o.user_id = {$_SESSION['user_id']}
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");


/* =========================
   FETCH USER FEEDBACK
========================= */
$feedbacks = $conn->query("
    SELECT f.comments, f.rating, f.created_at, o.order_id
    FROM feedback f
    JOIN orders o ON f.order_id = o.order_id
    WHERE f.user_id = $user_id
    ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Dashboard - Gown&Go</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="inclusion/stylesheet.css">
<style>
.dashboard-container {
    max-width: 1100px;
    margin: 40px auto;
    background: rgba(255,255,255,0.95);
    padding: 35px 40px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.section-title {
    font-family: 'Playfair Display', serif;
    color: #d86ca1;
    margin-bottom: 15px;
    margin-top: 30px;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
    gap: 15px;
}

.profile-box {
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.table {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
}
</style>
</head>

<body>

<?php include 'inclusion/nav.php'; ?>

<div class="dashboard-container">

<!-- ================= PROFILE ================= -->
<h2 class="section-title">My Profile</h2>
<div class="profile-grid">
    <div class="profile-box"><strong>Name:</strong><br><?php echo htmlspecialchars($user['username']); ?></div>
    <div class="profile-box"><strong>Email:</strong><br><?php echo htmlspecialchars($user['email']); ?></div>
    <div class="profile-box"><strong>Address:</strong><br><?php echo htmlspecialchars($user['address']); ?></div>
    <div class="profile-box"><strong>Contact No:</strong><br><?php echo htmlspecialchars($user['contact_no']); ?></div>
</div>

<!-- ================= RECENT TRANSACTIONS ================= -->
<h2 class="section-title">Recent Transactions</h2>

<table class="table table-bordered">
<thead style="background:#f9e6f1;">
<tr>
    <th>Order #</th>
    <th>Type</th>
    <th>Return Status</th>
    <th>Status</th>
    <th>Total (₱)</th>
    <th>Date</th>
    <th>Items</th>
</tr>
</thead>
<tbody>

<?php if ($orders->num_rows == 0): ?>
<tr><td colspan="7" class="text-center">No transactions yet.</td></tr>
<?php else: ?>
<?php while($o = $orders->fetch_assoc()): ?>
<tr>
    <!--<td>#<?php echo $o['order_id']; ?></td>
    <td><?php echo $o['order_type']; ?></td>
    <td><?php echo $o['payment_status'] ?? 'Unpaid'; ?></td>
    <td>
        <?php 
            if ($o['order_type'] === 'Rental') {
                echo $o['return_status'];
            } else {
                echo 'N/A';
            }
        ?>
    </td>
    <td><?php echo $o['order_status']; ?></td>
    <td><?php echo number_format($o['total_amount'], 2); ?></td>
    <td><?php echo $o['order_date']; ?></td>
        -->
    <td>#<?= $o['order_id']; ?></td>
<td><?= $o['order_type']; ?></td>
<td><?= $o['order_type'] === 'Rental' ? $o['return_status'] : 'N/A'; ?></td>
<td><?= $o['order_status']; ?></td>
<td><?= number_format($o['total_amount'],2); ?></td>
<td><?= $o['order_date']; ?></td>
<td><?= htmlspecialchars($o['item_names']); ?></td>

</tr>


<?php endwhile; ?>
<?php endif; ?>

</tbody>
</table>

<!-- ================= MY FEEDBACK ================= -->
<h2 class="section-title">My Feedback</h2>

<table class="table table-bordered">
<thead style="background:#f9e6f1;">
<tr>
    <th>Order #</th>
    <th>Rating</th>
    <th>Comment</th>
    <th>Date</th>
</tr>
</thead>
<tbody>

<?php if ($feedbacks->num_rows == 0): ?>
<tr><td colspan="4" class="text-center">You have not submitted feedback yet.</td></tr>
<?php else: ?>
<?php while($f = $feedbacks->fetch_assoc()): ?>
<tr>
    <td>#<?php echo $f['order_id']; ?></td>
    <td><?php echo $f['rating']; ?>/5</td>
    <td><?php echo htmlspecialchars($f['comments']); ?></td>
    <td><?php echo $f['created_at']; ?></td>
</tr>
<?php endwhile; ?>
<?php endif; ?>

</tbody>
</table>

</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
