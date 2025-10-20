<?php
session_start();
include 'connectdb.php'; // ใช้ connectdb.php

// ตรวจสอบว่าเป็น POST request และมี product_id ส่งมา
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {

    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; // ค่าเริ่มต้นคือ 1
    $selected_switch = $_POST['selected_switch'] ?? null; // รับค่าสวิตช์ (ถ้ามี)

    // ป้องกันการใส่จำนวนติดลบ
    if ($quantity <= 0) {
        $quantity = 1;
    }

    // --- ดึงข้อมูลสินค้าจาก DB เพื่อใช้แสดงในตะกร้า ---
    $stmt = $conn->prepare("SELECT name, price, image_url, stock_quantity FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // ตรวจสอบสต็อก
        if ($quantity > $product['stock_quantity']) {
             $_SESSION['message'] = "Sorry, only " . $product['stock_quantity'] . " items available for " . htmlspecialchars($product['name']);
             $_SESSION['message_type'] = "warning"; // ใช้ warning หรือ danger
             header("Location: product_detail.php?id=" . $product_id); // กลับไปหน้าสินค้าเดิม
             exit();
        }


        // --- สร้าง Key สำหรับเก็บใน Session Cart ---
        // ใช้ ID + Switch (ถ้ามี) เพื่อแยกสินค้าชนิดเดียวกันแต่คนละสวิตช์
        $cart_key = $product_id;
        if (!empty($selected_switch)) {
            $cart_key .= '_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $selected_switch); // ทำความสะอาดชื่อสวิตช์เล็กน้อย
        }

        // --- เตรียมข้อมูลสินค้าที่จะเก็บลง Session ---
        $cart_item = [
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image_url'],
            'quantity' => $quantity,
            'switch' => $selected_switch
        ];

        // --- เพิ่ม/อัปเดต สินค้าใน Session Cart ---
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // ถ้ายังไม่มีตะกร้า ให้สร้าง array ว่างๆ ก่อน
        }

        if (isset($_SESSION['cart'][$cart_key])) {
            // ถ้ามีสินค้านี้ (และสวิตช์เดียวกัน) อยู่แล้ว ให้อัปเดตจำนวน
            // ตรวจสอบสต็อกอีกครั้งเผื่อกรณีเพิ่มซ้ำ
             $new_quantity = $_SESSION['cart'][$cart_key]['quantity'] + $quantity;
             if ($new_quantity > $product['stock_quantity']) {
                 $_SESSION['message'] = "Cannot add more. Only " . $product['stock_quantity'] . " items available for " . htmlspecialchars($product['name']) . ($selected_switch ? ' (' . htmlspecialchars($selected_switch) . ' Switch)' : '');
                 $_SESSION['message_type'] = "warning";
             } else {
                 $_SESSION['cart'][$cart_key]['quantity'] = $new_quantity;
                 $_SESSION['message'] = htmlspecialchars($product['name']) . " quantity updated in cart.";
                 $_SESSION['message_type'] = "info"; // ใช้ info หรือ success
             }

        } else {
            // ถ้ายังไม่มี ให้เพิ่มเข้าไปใหม่
            $_SESSION['cart'][$cart_key] = $cart_item;
            $_SESSION['message'] = htmlspecialchars($product['name']) . " added to cart!";
            $_SESSION['message_type'] = "success";
        }

    } else {
        // ไม่พบ Product ID นี้
        $_SESSION['message'] = "Product not found.";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();

    // ส่งกลับไปหน้าสินค้าเดิม (เพื่อให้เห็น pop up หรือข้อความ)
    header("Location: product_detail.php?id=" . $product_id);
    exit();

} else {
    // ถ้าไม่ได้เข้ามาแบบ POST หรือไม่มี product_id
    header("Location: index.php"); // ไปหน้าแรก
    exit();
}
?>