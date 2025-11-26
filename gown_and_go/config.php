<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "gown_and_go";

try {
    $conn = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    error_log("DB Connection Failed: " . $e->getMessage());
    die("Database connection error.");
}
