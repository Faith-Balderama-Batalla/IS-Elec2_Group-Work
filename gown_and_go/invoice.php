<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int) $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order header
$sql = "
    SELECT o.*, u.username, u.email, u.contact_no, u.address
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ? AND o.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Fetch order items
$sql2 = "
    SELECT od.*, i.name
    FROM order_details od
    JOIN items i ON od.item_id = i.item_id
    WHERE od.order_id = ?
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$items = $stmt2->get_result();
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
        margin: 0;
        background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
        background-size: cover;
    }
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(245,230,240,0.45);
        z-index: -1;
    }

    .invoice-box {
        max-width: 900px;
        margin: 40px auto;
        background: rgba(255,255,255,0.92);
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(183,134,154,0.4);
    }

    h2 {
        text-align: center;
        font-family: 'Playfair Display', serif;
        color: #d86ca1;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
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

    .buttons {
        margin-top: 20px;
        text-align: center;
    }
    .btn {
        padding: 10px 16px;
        background: #d86ca1;
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        margin: 5px;
    }
    .btn:hover {
        background: #b3548a;
    }
</style>
</head>

<body>

<div class="invoice-box">

    <h2>Invoice #<?php echo $order_id; ?></h2>

    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
    <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
    <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>

    <table>
        <tr>
            <th>Item</th>
            <th>Order Type</th>
            <th>Unit Price (₱)</th>
            <th>Qty</th>
            <th>Rental Days</th>
            <th>Subtotal (₱)</th>
        </tr>

        <?php while ($row = $items->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo $row['order_type']; ?></td>
            <td>₱<?php echo number_format($row['unit_price'],2); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td>
                <?php 
                    echo ($row['order_type'] === "Rental") 
                        ? $row['rental_period_days'] . " days" 
                        : "-";
                ?>
            </td>
            <td>₱<?php echo number_format($row['subtotal'],2); ?></td>
        </tr>
        <?php endwhile; ?>

        <tr>
            <td colspan="5" align="right"><strong>Total:</strong></td>
            <td><strong>₱<?php echo number_format($order['total_amount'],2); ?></strong></td>
        </tr>
    </table>

    <div class="buttons">
        <button class="btn" onclick="window.print()">Print</button>
        <button class="btn" onclick="window.location.href='orders.php'">Back to Orders</button>
    </div>

</div>

</body>
</html>
