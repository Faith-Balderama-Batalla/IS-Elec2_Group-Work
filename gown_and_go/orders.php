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

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="inclusion/stylesheet.css">

    <style>

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

    <?php include 'inclusion/nav.php'; ?>

    <div class="main-container">

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
                        echo '<a class="btn" href="feedback.php?order_id=' . $row['order_id'] . '">Feedback</a>';
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
