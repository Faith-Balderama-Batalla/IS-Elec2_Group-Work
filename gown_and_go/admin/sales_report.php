<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* =======================
      FETCH SALES DATA
========================== */

// Total orders
$res = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
$total_orders = ($res->num_rows) ? $res->fetch_assoc()['total_orders'] : 0;

// Total revenue (PAID only)
$res = $conn->query("
    SELECT COALESCE(SUM(amount), 0) AS total_revenue 
    FROM payments 
    WHERE payment_status = 'Paid'
");
$total_revenue = ($res->num_rows) ? $res->fetch_assoc()['total_revenue'] : 0;

// Count rentals
$res = $conn->query("SELECT COUNT(*) AS rentals FROM order_details WHERE order_type = 'Rent'");
$rentals_count = ($res->num_rows) ? $res->fetch_assoc()['rentals'] : 0;

// Count purchases
$res = $conn->query("SELECT COUNT(*) AS purchases FROM order_details WHERE order_type = 'Purchase'");
$purchase_count = ($res->num_rows) ? $res->fetch_assoc()['purchases'] : 0;

$recent_sales = $conn->query("
    SELECT DATE(order_date) AS day, SUM(total_amount) AS daily_total
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(order_date)
    ORDER BY day DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Report - GOWN&GO Admin</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="inclusion/stylesheet.css">

<style>
    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .card h3 {
        margin: 0;
        color: #999;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .card .value {
        font-size: 1.6rem;
        font-weight: bold;
        color: #6b2b4a;
        margin-top: 10px;
    }
</style>
</head>

<body>
<?php include 'inclusion/nav.php'; ?>

<div class="main-container">
    <h2>Sales & Inventory Report</h2>

    <div class="grid">
        <div class="card">
            <h3>Total Orders</h3>
            <div class="value"><?php echo $total_orders; ?></div>
        </div>

        <div class="card">
            <h3>Total Revenue</h3>
            <div class="value">₱<?php echo number_format($total_revenue,2); ?></div>
        </div>
    </div>

    <h3 style="color:#d86ca1; margin-top:35px;">Daily Sales</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Total (₱)</th>
        </tr>
        <?php if ($recent_sales->num_rows == 0): ?>
            <tr><td colspan="2">No recent sales.</td></tr>
        <?php else: ?>
            <?php while($row = $recent_sales->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['day']; ?></td>
                <td>₱<?php echo number_format($row['daily_total'],2); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
