<?php
session_start();
include 'connectdb.php';

// (ฟังก์ชัน uploadProductImage ... เหมือนเดิม)
function uploadProductImage($file) { /* ... function code ... */ }


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = $_POST['product_id'] ?? 0;
    $old_image_url = $_POST['old_image_url'] ?? NULL;

    if (empty($product_id)) { /* ... error handling ... */ header("Location: dashboard.php"); exit(); }

    // --- 2. ตรวจสอบการอัปโหลดไฟล์ใหม่ (เหมือนเดิม) ---
    $image_url_to_db = $old_image_url;
    $new_image_path = uploadProductImage($_FILES['product_image']);
    if ($new_image_path !== null) {
        $image_url_to_db = $new_image_path;
        if (!empty($old_image_url) && file_exists($old_image_url)) { @unlink($old_image_url); }
    } elseif (isset($_SESSION['message'])) {
        header("Location: edit_product.php?id=" . $product_id); exit();
    }

    // --- 3. รับค่าจากฟอร์ม (เวอร์ชันล่าสุด) ---
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? NULL;
    $price = $_POST['price'] ?? 0.00;
    $stock_quantity = $_POST['stock_quantity'] ?? 0;
    $category = $_POST['category'] ?? '';
    $keyboard_size = $_POST['keyboard_size'] ?? NULL;
    $sub_category = $_POST['sub_category'] ?? NULL; // <-- 1. เพิ่มรับ sub_category
    $available_switches = $_POST['available_switches'] ?? NULL;

    // แปลงค่าว่างให้เป็น NULL
    $description = empty($description) ? NULL : $description;
    $keyboard_size = empty($keyboard_size) ? NULL : $keyboard_size;
    $sub_category = empty($sub_category) ? NULL : $sub_category; // <-- 2. แปลงค่าว่าง sub_category
    $available_switches = empty($available_switches) ? NULL : $available_switches;
    $image_url_to_db = empty($image_url_to_db) ? NULL : $image_url_to_db;

    // --- 4. อัปเดต DB (เวอร์ชันล่าสุด) ---
    // เพิ่ม sub_category = ? ใน UPDATE statement
    $sql = "UPDATE products SET
            name = ?, description = ?, price = ?, stock_quantity = ?,
            category = ?, keyboard_size = ?, sub_category = ?, available_switches = ?, image_url = ?
            WHERE product_id = ?"; // <-- 3. เพิ่ม field (รวม 9 fields + product_id)

    $stmt = $conn->prepare($sql);

    // "ssdis" + "s" + "s" + "s" + "s" + "i" = ssdisssssi (10 ตัว)
    $stmt->bind_param("ssdisssssi", // <-- 4. แก้ไข bind_param type string (10 characters)
        $name, $description, $price, $stock_quantity,
        $category, $keyboard_size, $sub_category, $available_switches, $image_url_to_db, // <-- 5. เพิ่ม $sub_category
        $product_id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "อัปเดตสินค้า ID: $product_id สำเร็จ!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปเดต: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();
    header("Location: dashboard.php");
    exit();

} else {
    header("Location: dashboard.php");
    exit();
}
?>