<?php
session_start();
include 'connectdb.php'; // ใช้ connectdb.php

// --- การป้องกันเบื้องต้น ---
// 1. ตรวจสอบว่าเป็น POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: checkout.php");
    exit();
}

// 2. ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to place an order.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 3. ตรวจสอบว่าตะกร้าว่างหรือไม่
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty.";
    $_SESSION['message_type'] = "info";
    header("Location: cart.php");
    exit();
}

// --- รับข้อมูลจากฟอร์ม ---
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$address1 = $_POST['address1'] ?? '';
$address2 = $_POST['address2'] ?? '';
$city = $_POST['city'] ?? '';
$province = $_POST['province'] ?? '';
$zip_code = $_POST['zip_code'] ?? '';
$payment_method = $_POST['paymentMethod'] ?? 'cod'; // Default to COD

// --- START: เพิ่มรับค่าราคาที่คำนวณแล้วจาก checkout.php ---
// ค่าเหล่านี้ส่งมาจาก hidden fields ใน checkout.php และเป็นค่าสุดท้ายที่ลูกค้าเห็น
// ควรใช้ filter_var เพื่อแปลงเป็น float และตรวจสอบความปลอดภัย
$subtotal = filter_var($_POST['subtotal'] ?? 0.00, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$discount_amount = filter_var($_POST['discount_amount'] ?? 0.00, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$shipping_cost = filter_var($_POST['shipping_cost'] ?? 0.00, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$grand_total = filter_var($_POST['grand_total'] ?? 0.00, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
// --- END: เพิ่มรับค่าราคา ---


// (เพิ่ม Validation ข้อมูลที่รับมา เช่น ตรวจสอบว่ากรอกครบหรือไม่)
if (empty($first_name) || empty($last_name) || empty($phone) || empty($address1) || empty($city) || empty($province) || empty($zip_code)) {
     $_SESSION['message'] = "Please fill in all required shipping address fields.";
     $_SESSION['message_type'] = "danger";
     header("Location: checkout.php");
     exit();
}

// --- ตรวจสอบความถูกต้องของ Grand Total อีกครั้ง (Optional แต่แนะนำ) ---
// ใช้ค่า Grand Total ที่ส่งมา
$final_total_amount = $grand_total; 

// --- เตรียมข้อมูลสำหรับบันทึก ---
// รวมที่อยู่
$shipping_address = $first_name . " " . $last_name . "\n";
$shipping_address .= "Phone: " . $phone . "\n";
$shipping_address .= $address1 . "\n";
if (!empty($address2)) {
    $shipping_address .= $address2 . "\n";
}
$shipping_address .= $city . ", " . $province . " " . $zip_code;

$order_status = 'Pending'; // สถานะเริ่มต้น


// --- !!! เริ่ม Database Transaction !!! ---
$conn->begin_transaction();

try {
    // 1. INSERT ข้อมูลลงตาราง `orders`
    $sql_order = "INSERT INTO orders (user_id, subtotal, discount_amount, shipping_cost, total_amount, status, shipping_address) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_order = $conn->prepare($sql_order);
    
    // *** แก้ไขตรงนี้: เพิ่ม 'd' สำหรับ $final_total_amount เพื่อให้รวมเป็น 7 ตัวอักษร: i d d d d s s ***
    $stmt_order->bind_param("iddddss", $user_id, $subtotal, $discount_amount, $shipping_cost, $final_total_amount, $order_status, $shipping_address);
    
    if (!$stmt_order->execute()) {
        throw new Exception("Error inserting order: " . $stmt_order->error);
    }
    
    // ดึง ID ของออเดอร์ที่เพิ่งสร้าง
    $order_id = $conn->insert_id;
    $stmt_order->close();

    // เตรียม Statement สำหรับ order_items และ update stock (ใช้ซ้ำใน loop)
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price_per_unit, selected_switch) VALUES (?, ?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);

    $sql_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ? AND stock_quantity >= ?";
    $stmt_stock = $conn->prepare($sql_stock);


    // 2. วนลูปสินค้าในตะกร้า (Session) เพื่อ INSERT ลง `order_items` และ UPDATE `products` (ตัดสต็อก)
    foreach ($_SESSION['cart'] as $key => $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price_per_unit = $item['price']; // ใช้ราคาจากตอน Add to Cart
        $selected_switch = $item['switch'] ?? NULL; // ใช้ ?? NULL เพื่อจัดการกรณีที่ไม่มี switch

        // 2.1 INSERT ลง order_items
        $stmt_items->bind_param("iiids", $order_id, $product_id, $quantity, $price_per_unit, $selected_switch);
        if (!$stmt_items->execute()) {
             throw new Exception("Error inserting order item (PID: $product_id): " . $stmt_items->error);
        }

        // 2.2 UPDATE (ลด) stock ใน products
        // WHERE stock_quantity >= ? เพื่อป้องกันกรณีสต็อกติดลบ (Double Check)
        $stmt_stock->bind_param("iii", $quantity, $product_id, $quantity);
        if (!$stmt_stock->execute()) {
            throw new Exception("Error updating stock (PID: $product_id): " . $stmt_stock->error);
        }
        // ตรวจสอบว่าการ UPDATE stock สำเร็จจริงหรือไม่ (มีแถวที่ถูกแก้ไหม)
        if ($stmt_stock->affected_rows === 0) {
             throw new Exception("Insufficient stock for product ID: $product_id (Required: $quantity). Order cancelled.");
        }
    }
    $stmt_items->close();
    $stmt_stock->close();

    // 3. ถ้าทุกอย่างสำเร็จ -> Commit Transaction
    $conn->commit();

    // 4. ล้างตะกร้าสินค้าใน Session
    unset($_SESSION['cart']);

    // 5. ส่งต่อไปหน้า Thank You พร้อม Order ID
    $_SESSION['message'] = "Your order (#$order_id) has been placed successfully!";
    $_SESSION['message_type'] = "success";
    header("Location: thank_you.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    // 6. ถ้ามี Error เกิดขึ้น -> Rollback Transaction
    $conn->rollback();

    // 7. เก็บ Error Message แล้วส่งกลับไปหน้า Checkout
    // ใช้ getMessage() เพื่อแสดงข้อความที่กำหนดเอง (เช่น Insufficient stock)
    $_SESSION['message'] = "Failed to place order: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    
    $conn->close(); // ปิด Connection ก่อน Redirect
    header("Location: checkout.php");
    exit();
}

?>