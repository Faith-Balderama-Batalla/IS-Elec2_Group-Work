<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid order detail.");
}

$order_detail_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    UPDATE order_details 
    SET return_status = 'Returned' 
    WHERE order_detail_id = ?
");
$stmt->bind_param("i", $order_detail_id);
$stmt->execute();

header("Location: dashboard.php?msg=Rental+marked+as+returned");
exit;
