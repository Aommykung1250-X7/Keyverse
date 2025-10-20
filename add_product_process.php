<?php
session_start();
include 'connectdb.php'; 

// (ฟังก์ชัน uploadProductImage ... เหมือนเดิม)
function uploadProductImage($file) {
    $upload_dir = 'uploads/'; 
    if (isset($file) && $file['error'] == UPLOAD_ERR_OK) {
        $check = getimagesize($file["tmp_name"]);
        if($check === false) { $_SESSION['message'] = "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ"; $_SESSION['message_type'] = "danger"; return null; }
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $new_filename = time() . '_' . $safe_filename . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $target_file; 
        } else {
            $_SESSION['message'] = "เกิดข้อผิดพลาดขณะอัปโหลดไฟล์"; $_SESSION['message_type'] = "danger"; return null;
        }
    }
    return null; 
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $image_url_to_db = uploadProductImage($_FILES['product_image']);
    if ($image_url_to_db === null && isset($_SESSION['message'])) {
        header("Location: add_product.php"); 
        exit();
    }
    
    // --- รับค่าจากฟอร์ม (เวอร์ชันใหม่) ---
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? NULL;
    $price = $_POST['price'] ?? 0.00;
    $stock_quantity = $_POST['stock_quantity'] ?? 0;
    $category = $_POST['category'] ?? '';
    $keyboard_size = $_POST['keyboard_size'] ?? NULL; // <-- 1. เพิ่มตัวแปร
    $available_switches = $_POST['available_switches'] ?? NULL;

    // แปลงค่าว่างให้เป็น NULL
    $description = empty($description) ? NULL : $description;
    $keyboard_size = empty($keyboard_size) ? NULL : $keyboard_size; // <-- 2. แปลงค่าว่าง
    $available_switches = empty($available_switches) ? NULL : $available_switches;
    $image_url_to_db = empty($image_url_to_db) ? NULL : $image_url_to_db;

    // --- 3. บันทึกลง DB (เวอร์ชันใหม่) ---
    $sql = "INSERT INTO products (name, description, price, stock_quantity, category, keyboard_size, available_switches, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; // <-- 3. เพิ่ม field และ ?
    
    $stmt = $conn->prepare($sql);
    
    // "ssdiss" + "s" + "s" + "s" = ssdissss (8 ตัว)
    $stmt->bind_param("ssdissss", // <-- 4. แก้ไข bind_param
        $name, $description, $price, $stock_quantity, $category, 
        $keyboard_size, $available_switches, $image_url_to_db
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "เพิ่มสินค้า '" . htmlspecialchars($name) . "' สำเร็จ!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการเพิ่มสินค้า: " . $stmt->error;
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