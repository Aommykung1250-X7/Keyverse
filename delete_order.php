<?php
session_start();
include 'connectdb.php'; // ใช้ connectdb.php

// 1. ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// 2. ตรวจสอบว่ามี Order ID ส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ไม่ได้ระบุ Order ID ที่ถูกต้อง";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}
$order_id = (int)$_GET['id'];

// --- !!! เริ่ม Database Transaction !!! ---
$conn->begin_transaction();

try {
    // 1. ลบรายการสินค้าที่เกี่ยวข้อง (Foreign Key Constraint)
    // ต้องลบจากตาราง order_items ก่อน เนื่องจากมีการผูก Order ID ไว้
    $sql_items = "DELETE FROM order_items WHERE order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);

    if (!$stmt_items->execute()) {
        throw new Exception("Error deleting order items: " . $stmt_items->error);
    }
    $stmt_items->close();

    // 2. ลบรายการสั่งซื้อหลัก
    $sql_order = "DELETE FROM orders WHERE order_id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $order_id);

    if (!$stmt_order->execute()) {
        throw new Exception("Error deleting order: " . $stmt_order->error);
    }

    // 3. ตรวจสอบว่าลบรายการหลักสำเร็จหรือไม่
    if ($stmt_order->affected_rows === 0) {
        throw new Exception("Order ID $order_id not found or already deleted.");
    }
    $stmt_order->close();
    
    // 4. ถ้าทุกอย่างสำเร็จ -> Commit Transaction
    $conn->commit();

    // 5. แจ้งเตือนความสำเร็จ
    $_SESSION['message'] = "ออเดอร์ #$order_id ถูกลบออกจากระบบอย่างถาวรแล้ว";
    $_SESSION['message_type'] = "success";
    header("Location: dashboard.php#history"); // กลับไปหน้า History
    exit();

} catch (Exception $e) {
    // 6. ถ้ามี Error เกิดขึ้น -> Rollback Transaction
    $conn->rollback();

    // 7. แจ้งเตือนข้อผิดพลาด
    $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบออเดอร์ #$order_id: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    
    $conn->close();
    header("Location: dashboard.php");
    exit();
}

?>