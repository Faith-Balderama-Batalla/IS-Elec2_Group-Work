<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    die("Order ID missing.");
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order
$sql = "
    SELECT o.*, u.username, u.email, u.address, u.contact_no,
           p.payment_method, p.payment_status, p.amount AS payment_amount
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    WHERE o.order_id = ? AND o.user_id = ?
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found or you do not have permission to view it.");
}

// Fetch items
$sql_items = "
    SELECT i.name, i.purchase_price, od.quantity, od.subtotal
    FROM order_details od
    JOIN items i ON od.item_id = i.item_id
    WHERE od.order_id = ?
";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice #<?php echo $order_id; ?> - GOWN&GO</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #faf5f8;
    color: #5a2e40;
    margin: 0;
    padding: 0;
}
.invoice-container {
    max-width: 900px;
    margin: 30px auto;
    background: #ffffff;
    padding: 35px 45px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(183, 134, 154, 0.3);
}
.header {
    text-align: center;
    margin-bottom: 25px;
}
.header .logo {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    font-weight: 700;
    color: #d86ca1;
}
.section-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-top: 25px;
    color: #5a2e40;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}
th, td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}
th {
    text-align: left;
    background: #f9e6f1;
    color: #6b2b4a;
}
.total-row td {
    font-weight: bold;
    font-size: 1.05rem;
}
.print-btn {
    background-color: #d86ca1;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 25px;
}
.print-btn:hover {
    background-color: #b3548a;
}
.info-box {
    background: #fdf1f7;
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="invoice-container">
    
    <div class="header">
        <div class="logo">GOWN&GO</div>
        <p>Official Receipt / Invoice</p>
    </div>

    <div class="info-box">
        <strong>Order #:</strong> <?php echo $order['order_id']; ?><br>
        <strong>Date:</strong> <?php echo $order['order_date']; ?><br>
        <strong>Status:</strong> <?php echo $order['order_status']; ?><br>
    </div>

    <h3 class="section-title">Customer Details</h3>
    <div class="info-box">
        <strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?><br>
        <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
        <strong>Contact:</strong> <?php echo htmlspecialchars($order['contact_no']); ?><br>
        <strong>Delivery Address:</strong><br>
        <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?>
    </div>

    <h3 class="section-title">Items Ordered</h3>
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
            <?php while ($row = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo number_format($row['purchase_price'], 2); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo number_format($row['subtotal'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
            <tr class="total-row">
                <td colspan="3" align="right">Total Amount:</td>
                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <h3 class="section-title">Payment Details</h3>
    <div class="info-box">
        <strong>Method:</strong> <?php echo $order['payment_method']; ?><br>
        <strong>Status:</strong> <?php echo $order['payment_status']; ?><br>
        <strong>Amount Paid:</strong> ₱<?php echo number_format($order['payment_amount'], 2); ?><br>
    </div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>
    <button class="print-btn" onclick="window.location.href='orders.php'">Exit</button>


</div>

</body>
</html>
