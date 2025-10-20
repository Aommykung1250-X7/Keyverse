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

    $product_id = $_POST['product_id'] ?? 0;
    $old_image_url = $_POST['old_image_url'] ?? NULL;
    
    if (empty($product_id)) {
        // ... (โค้ด error เหมือนเดิม) ...
        header("Location: dashboard.php");
        exit();
    }

    // --- 2. ตรวจสอบการอัปโหลดไฟล์ใหม่ ---
    $image_url_to_db = $old_image_url; 
    $new_image_path = uploadProductImage($_FILES['product_image']); 

    if ($new_image_path !== null) {
        $image_url_to_db = $new_image_path; 
        if (!empty($old_image_url) && file_exists($old_image_url)) {
            @unlink($old_image_url); 
        }
    } elseif (isset($_SESSION['message'])) {
        header("Location: edit_product.php?id=" . $product_id);
        exit();
    }

    // --- 3. รับค่าจากฟอร์ม (เวอร์ชันใหม่) ---
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

    // --- 4. อัปเดต DB (เวอร์ชันใหม่) ---
    $sql = "UPDATE products SET 
            name = ?, description = ?, price = ?, stock_quantity = ?, 
            category = ?, keyboard_size = ?, available_switches = ?, image_url = ?
            WHERE product_id = ?"; // <-- 3. เพิ่ม field
    
    $stmt = $conn->prepare($sql);
    
    // "ssdis" + "s" + "s" + "s" + "i" = ssdissssi (9 ตัว)
    $stmt->bind_param("ssdissssi", // <-- 4. แก้ไข bind_param
        $name, $description, $price, $stock_quantity, 
        $category, $keyboard_size, $available_switches, $image_url_to_db,
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