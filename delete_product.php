<?php
session_start();
include 'connectdb.php';

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "คุณไม่มีสิทธิ์ดำเนินการนี้";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}

// 1. ตรวจสอบว่ามี ID ส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ไม่ได้ระบุ ID สินค้าที่จะลบ";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}

$product_id = $_GET['id'];

// !! ข้อควรระวัง: ก่อนลบสินค้า ควรตรวจสอบว่าสินค้านี้อยู่ใน Order หรือไม่
// (ในระบบจริง อาจจะแค่ "ซ่อน" สินค้า แทนที่จะ "ลบ" จริง)
// แต่ตามโจทย์คือให้ลบ:

// 2. เตรียม SQL (ใช้ DELETE)
$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "ลบสินค้า ID: $product_id สำเร็จแล้ว";
    $_SESSION['message_type'] = "success";
} else {
    // อาจจะลบไม่สำเร็จ ถ้าสินค้านี้ถูกอ้างอิงในตาราง order_items
    $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบ: " . $stmt->error . " (อาจมีสินค้านี้อยู่ในออเดอร์เก่า)";
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
$conn->close();
header("Location: dashboard.php"); // เด้งกลับไปหน้า dashboard
exit();
?>