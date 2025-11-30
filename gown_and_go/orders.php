<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders grouped with item names
$sql = "
    SELECT 
        o.order_id,
        o.order_date,
        o.order_status,
        o.total_amount,
        GROUP_CONCAT(
            CONCAT(
                i.name, 
                ' (', od.order_type, ') x', od.quantity
            ) SEPARATOR ', '
        ) AS items
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
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - GOWN&GO</title>

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

    .main-box {
        max-width: 1000px;
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
        font-size: 0.95rem;
        margin-top: 20px;
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
        padding: 6px 12px;
        background: #d86ca1;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        margin: 3px 0;
    }
    .btn:hover {
        background: #b3548a;
    }

</style>
</head>

<body>

<header class="topbar">
    <div class="logo">GOWN&GO</div>
    <div>
        <a href="client_home.php">Shop</a>
        <a href="cart.php">Cart</a>
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="main-box">

<h2>My Orders</h2>

<?php if ($result->num_rows === 0): ?>
    <p>You have no orders yet.</p>

<?php else: ?>

<table>
    <tr>
        <th>Order #</th>
        <th>Date</th>
        <th>Items</th>
        <th>Status</th>
        <th>Total (₱)</th>
        <th>Invoice</th>
        <th>Feedback</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $row['order_id']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php echo $row['items']; ?></td>
        <td><?php echo $row['order_status']; ?></td>
        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>

        <!-- Invoice Button -->
        <td>
            <a class="btn" href="invoice.php?order_id=<?php echo $row['order_id']; ?>">View</a>
        </td>

        <!-- Feedback Button -->
        <td>
            <?php
            if ($row['order_status'] === "Completed") {

                // Check if feedback already exists
                $check_fb = $conn->prepare("SELECT feedback_id FROM feedback WHERE order_id = ?");
                $check_fb->bind_param("i", $row['order_id']);
                $check_fb->execute();
                $fb_res = $check_fb->get_result();

                if ($fb_res->num_rows === 0) {
                    echo '<a class="btn" href="feedback.php?order_id=' . $row['order_id'] . '">Leave Feedback</a>';
                } else {
                    echo '<span style="color:green; font-weight:bold;">Submitted</span>';
                }
            } else {
                echo '<span style="color:#888;">Unavailable</span>';
            }
            ?>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

<?php endif; ?>

</div>

</body>
</html>
