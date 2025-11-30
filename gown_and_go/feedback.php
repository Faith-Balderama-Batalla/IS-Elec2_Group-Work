<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order.");
}

$order_id = (int) $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Check if the order belongs to the user + is completed
$check = $conn->prepare("
    SELECT order_status 
    FROM orders 
    WHERE order_id = ? AND user_id = ?
");
$check->bind_param("ii", $order_id, $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    die("Order not found.");
}

$order = $res->fetch_assoc();
if ($order['order_status'] !== "Completed") {
    die("Feedback allowed only for completed orders.");
}

// Check if feedback already exists
$check2 = $conn->prepare("SELECT feedback_id FROM feedback WHERE order_id = ?");
$check2->bind_param("i", $order_id);
$check2->execute();
$res2 = $check2->get_result();
if ($res2->num_rows > 0) {
    die("Feedback already submitted.");
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $rating = (int) $_POST['rating'];
    $comments = trim($_POST['comments']);

    if ($rating < 1 || $rating > 5) {
        $error = "Rating must be between 1 and 5.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO feedback (user_id, order_id, comments, rating)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iisi", $user_id, $order_id, $comments, $rating);

        if ($stmt->execute()) {
            $success = "Thank you! Your feedback was submitted.";
        } else {
            $error = "Error saving feedback.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Leave Feedback</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
    body {
        background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', sans-serif;
    }
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(255,255,255,0.6);
        z-index: -1;
    }

    .box {
        max-width: 600px;
        margin: 40px auto;
        background: rgba(255,255,255,0.95);
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(183,134,154,0.4);
    }
    h2 {
        text-align: center;
        font-family: 'Playfair Display', serif;
        color: #d86ca1;
    }
    textarea, select {
        width: 100%;
        padding: 8px;
        border-radius: 10px;
        border: 1px solid #ccc;
        margin-top: 10px;
        font-size: 0.95rem;
    }
    textarea {
        height: 120px;
        resize: vertical;
    }
    .btn {
        margin-top: 15px;
        background: #d86ca1;
        color: #fff;
        padding: 10px 18px;
        display: inline-block;
        border-radius: 10px;
        border: none;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
    }
    .btn:hover {
        background: #b3548a;
    }
    .msg {
        text-align: center;
        padding: 10px;
        background: #e8ffe8;
        color: #1e7a1e;
        border-radius: 10px;
    }
    .error {
        text-align: center;
        padding: 10px;
        background: #ffe8e8;
        color: #a40000;
        border-radius: 10px;
    }
</style>
</head>

<body>

<div class="box">
    <h2>Leave Feedback</h2>

    <?php if ($success): ?>
        <div class="msg"><?php echo $success; ?></div>
        <br><a class="btn" href="orders.php">Back to Orders</a>
    <?php else: ?>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Rating</label>
        <select name="rating" required>
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
        </select>

        <label>Comments</label>
        <textarea name="comments" placeholder="Write your feedback here..." required></textarea>

        <button class="btn" type="submit">Submit Feedback</button>
        <a href="orders.php" class="btn" style="background:#aaa;">Cancel</a>
    </form>

    <?php endif; ?>
</div>

</body>
</html>
