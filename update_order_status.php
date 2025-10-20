<?php
session_start();
include 'connectdb.php'; // ใช้ connectdb.php

// 1. ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "คุณไม่มีสิทธิ์ดำเนินการนี้";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}

// 2. ตรวจสอบว่ามี ID และ Status ส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['status']) || empty($_GET['status'])) {
    $_SESSION['message'] = "ข้อมูลไม่ครบถ้วน (ID หรือ Status)";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}

$order_id = $_GET['id'];
$new_status = $_GET['status'];

// 3. (ทางเลือก) ตรวจสอบว่า status ที่ส่งมาถูกต้อง
$allowed_statuses = ['Processing', 'Shipped', 'Completed', 'Cancelled'];
if (!in_array($new_status, $allowed_statuses)) {
    $_SESSION['message'] = "สถานะที่ส่งมาไม่ถูกต้อง";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}

// 4. อัปเดตฐานข้อมูล
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$stmt->bind_param("si", $new_status, $order_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "อัปเดตสถานะออเดอร์ #$order_id เป็น '$new_status' สำเร็จ";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปเดต: " . $stmt->error;
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();

// 5. ส่งกลับไปหน้า Dashboard (ตรงแท็บ Orders)
header("Location: dashboard.php#orders");
exit();
?>