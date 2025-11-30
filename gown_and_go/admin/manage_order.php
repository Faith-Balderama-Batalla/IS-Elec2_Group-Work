<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$query = "
    SELECT 
        o.order_id, o.order_status, o.order_date, o.total_amount,
        u.username,
        GROUP_CONCAT(CONCAT(i.name, ' x', od.quantity, ' (', od.order_type, ')') SEPARATOR ', ') AS items
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    JOIN order_details od ON o.order_id = od.order_id
    JOIN items i ON od.item_id = i.item_id
    WHERE 1
";


$query .= " GROUP BY o.order_id ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);

// Bind params (only if not empty)
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="inclusion/stylesheet.css">

    <style>
        .badge {
            padding: 3px 7px;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .completed { background: #d4f8d4; color: green; }
        .pending { background: #ffe6a1; color: #9a6400; }
    </style>
</head>

<body>

    <?php include 'inclusion/nav.php'; ?>

    <div class="main-container">

        <h2>Manage Orders</h2>
            
        <table>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Items</th>
                <th>Status</th>
                <th>Total (₱)</th>
                <th>Action</th>
            </tr>
            
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['order_id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['order_date']; ?></td>
                <td><?php echo $row['items']; ?></td>
            
                <td>
                    <?php if ($row['order_status'] === "Completed"): ?>
                        <span class="badge completed">Completed</span>
                    <?php else: ?>
                        <span class="badge pending"><?php echo $row['order_status']; ?></span>
                    <?php endif; ?>
                </td>
                    
                <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                    
                <td>
                    <?php if ($row['order_status'] !== "Completed"): ?>
                        <a class="btn" href="complete_order.php?id=<?php echo $row['order_id']; ?>">
                            Complete
                        </a>
                    <?php else: ?>
                        <span style="color:green; font-weight:bold;">Done</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
