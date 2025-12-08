<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid rental ID");
}

$order_detail_id = (int) $_GET['id'];

// ✅ UPDATE RETURN STATUS
$stmt = $conn->prepare("
    UPDATE order_details 
    SET return_status = 'Returned'
    WHERE order_detail_id = ?
");

$stmt->bind_param("i", $order_detail_id);
$stmt->execute();

// ✅ OPTIONAL: Also mark item as Available again (for realism)
$conn->query("
    UPDATE items 
    SET status = 'Available' 
    WHERE item_id = (
        SELECT item_id FROM order_details WHERE order_detail_id = $order_detail_id
    )
");

header("Location: dashboard.php?success=Rental+successfully+marked+as+returned");
exit;
