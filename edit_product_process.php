<?php
session_start();
include 'connectdb.php';

// (ฟังก์ชัน uploadProductImage ... เหมือนเดิม)
function uploadProductImage($file) {
    $upload_dir = 'uploads/';
    // Check if file was uploaded and no error
    if (isset($file) && $file['error'] == UPLOAD_ERR_OK) {
        // Check if it's really an image
        $check = @getimagesize($file["tmp_name"]); // Use @ to suppress warning if not image
        if($check === false) {
             $_SESSION['message'] = "File is not a valid image.";
             $_SESSION['message_type'] = "danger"; return null;
        }
        // Check file type (optional but recommended)
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($check['mime'], $allowed_types)) {
            $_SESSION['message'] = "Only JPG, PNG, WEBP files are allowed.";
            $_SESSION['message_type'] = "danger"; return null;
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); // Lowercase extension
        $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $new_filename = time() . '_' . $safe_filename . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;

        // Try to move the file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $target_file; // Return new path on success
        } else {
            // Check for specific move errors (optional debug)
            // error_log("Failed to move uploaded file: " . print_r(error_get_last(), true));
            $_SESSION['message'] = "Error uploading file. Check permissions or path.";
            $_SESSION['message_type'] = "danger"; return null;
        }
    } else if (isset($file) && $file['error'] != UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors (e.g., file too large)
        $_SESSION['message'] = "File upload error: code " . $file['error'];
        $_SESSION['message_type'] = "danger"; return null;
    }
    return null; // No file uploaded or error occurred previously
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- *** DEBUG POINT 1: Check received data *** ---
    // echo "<pre>POST Data:\n"; var_dump($_POST); echo "</pre>";
    // echo "<pre>FILES Data:\n"; var_dump($_FILES); echo "</pre>";
    // exit; // Stop execution here to see the output

    $product_id = $_POST['product_id'] ?? 0;
    $old_image_url = $_POST['old_image_url'] ?? NULL;

    if (empty($product_id)) { /* ... error handling ... */ header("Location: dashboard.php"); exit(); }

    // --- 2. ตรวจสอบการอัปโหลดไฟล์ใหม่ ---
    $image_url_to_db = $old_image_url; // Default to old image
    $new_image_path = uploadProductImage($_FILES['product_image'] ?? null); // Pass null if not set

    // Redirect back ONLY if upload was ATTEMPTED and FAILED with an error message
    if ($new_image_path === null && isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE && isset($_SESSION['message'])) {
         header("Location: edit_product.php?id=" . $product_id);
         exit();
     }

    // If upload was successful, update the path and delete the old image
    if ($new_image_path !== null) {
        $image_url_to_db = $new_image_path;
        // Delete old image only if it exists, is not empty, and is not the placeholder
        if (!empty($old_image_url) && $old_image_url !== 'img/placeholder.png' && file_exists($old_image_url)) {
            @unlink($old_image_url);
        }
    }
    // If no new file was uploaded ($new_image_path is null), $image_url_to_db remains $old_image_url

    // --- *** DEBUG POINT 2: Check the final image path before DB update *** ---
    // echo "<pre>Image path to save: "; var_dump($image_url_to_db); echo "</pre>";
    // exit; // Stop execution here

    // --- 3. รับค่าจากฟอร์ม (เวอร์ชันล่าสุด) ---
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? NULL;
    $price = $_POST['price'] ?? 0.00;
    $stock_quantity = $_POST['stock_quantity'] ?? 0;
    $category = $_POST['category'] ?? '';
    $keyboard_size = $_POST['keyboard_size'] ?? NULL;
    $sub_category = $_POST['sub_category'] ?? NULL;
    $available_switches = $_POST['available_switches'] ?? NULL;

    // แปลงค่าว่างให้เป็น NULL
    $description = empty($description) ? NULL : $description;
    $keyboard_size = empty($keyboard_size) ? NULL : $keyboard_size;
    $sub_category = empty($sub_category) ? NULL : $sub_category;
    $available_switches = empty($available_switches) ? NULL : $available_switches;
    $image_url_to_db = empty($image_url_to_db) ? NULL : $image_url_to_db; // Keep this check

    // --- 4. อัปเดต DB (เวอร์ชันล่าสุด) ---
    $sql = "UPDATE products SET
            name = ?, description = ?, price = ?, stock_quantity = ?,
            category = ?, keyboard_size = ?, sub_category = ?, available_switches = ?, image_url = ?
            WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Handle prepare error
        $_SESSION['message'] = "Database prepare error: " . $conn->error;
        $_SESSION['message_type'] = "danger";
        $conn->close();
        header("Location: edit_product.php?id=" . $product_id); // Redirect back to edit page
        exit();
    }


    $bind_result = $stmt->bind_param("ssdisssssi",
        $name, $description, $price, $stock_quantity,
        $category, $keyboard_size, $sub_category, $available_switches, $image_url_to_db,
        $product_id
    );

     if (!$bind_result) {
        // Handle bind error
        $_SESSION['message'] = "Database bind error: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
        $stmt->close();
        $conn->close();
        header("Location: edit_product.php?id=" . $product_id); // Redirect back to edit page
        exit();
    }


    if ($stmt->execute()) {
        $_SESSION['message'] = "อัปเดตสินค้า ID: $product_id สำเร็จ!";
        $_SESSION['message_type'] = "success";
    } else {
        // --- *** DEBUG POINT 3: Check DB execution error *** ---
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปเดต DB: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
        // echo "DB Error: " . $stmt->error; // Temporarily uncomment to see error directly
        // exit; // Stop execution
    }

    $stmt->close();
    $conn->close();
    header("Location: dashboard.php"); // Redirect back to dashboard
    exit();

} else {
    header("Location: dashboard.php");
    exit();
}
?>