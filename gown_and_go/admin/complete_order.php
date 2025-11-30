<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid order.");
}

$order_id = (int) $_GET['id'];

/* STEP 1 — Get the order total */
$stmt = $conn->prepare("SELECT total_amount FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Order not found.");
}

$order = $res->fetch_assoc();
$total_amount = $order['total_amount'];

/* STEP 2 — Mark order as Completed */
$stmt = $conn->prepare("UPDATE orders SET order_status = 'Completed' WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

/* STEP 3 — Insert payment */
$stmt = $conn->prepare("
    INSERT INTO payments (order_id, amount, payment_status) 
    VALUES (?, ?, 'Paid')
");
$stmt->bind_param("id", $order_id, $total_amount);
$stmt->execute();

/* STEP 4 — Compute updated totals for sales_reports */
$total_orders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];

$total_revenue = $conn->query("
    SELECT COALESCE(SUM(amount), 0) AS total 
    FROM payments 
    WHERE payment_status='Paid'
")->fetch_assoc()['total'];

/* STEP 5 — Insert log entry */
$stmt = $conn->prepare("
    INSERT INTO sales_reports (total_orders, total_revenue)
    VALUES (?, ?)
");
$stmt->bind_param("id", $total_orders, $total_revenue);
$stmt->execute();

/* DONE */
header("Location: dashboard.php?msg=Order+completed+and+logged+successfully");
exit;
