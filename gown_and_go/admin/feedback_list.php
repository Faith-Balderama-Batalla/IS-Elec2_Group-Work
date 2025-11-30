<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$fb = $conn->query("
    SELECT f.*, u.username
    FROM feedback f
    JOIN users u ON f.user_id = u.user_id
    ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<html lang="en">
    
<head>
<meta charset="UTF-8">
    <title>Feedback List - Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="inclusion/stylesheet.css">
</head>

<body>

    <?php include 'inclusion/nav.php'; ?>

    <div class="main-container">
        <h2>Customer Feedback</h2>

        <table>
            <tr>
                <th>User</th>
                <th>Order</th>
                <th>Rating</th>
                <th>Comments</th>
                <th>Date</th>
            </tr>

            <?php while ($row = $fb->fetch_assoc()): ?>

            <tr>
                <td><?php echo $row['username']; ?></td>
                <td>#<?php echo $row['order_id']; ?></td>
                <td><?php echo str_repeat("★", $row['rating']); ?></td>
                <td><?php echo htmlspecialchars($row['comments']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>

            <?php endwhile; ?>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
