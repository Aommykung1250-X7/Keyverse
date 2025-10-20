<?php
session_start();

// 1. ตรวจสอบว่ามี 'key' ส่งมาทาง URL หรือไม่
if (isset($_GET['key'])) {
    $key_to_remove = $_GET['key'];

    // 2. ตรวจสอบว่ามีตะกร้า (cart) และมี key นี้อยู่ในตะกร้าจริงหรือไม่
    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$key_to_remove])) {
        
        // 3. ดึงชื่อสินค้าออกมาก่อน (เผื่อใช้ในข้อความแจ้งเตือน)
        $removed_item_name = $_SESSION['cart'][$key_to_remove]['name'];

        // 4. ลบรายการสินค้านั้นออกจาก Array ตะกร้า
        unset($_SESSION['cart'][$key_to_remove]);

        // 5. (ทางเลือก) ตรวจสอบว่าตะกร้าว่างหรือยัง ถ้าว่างก็ลบ $_SESSION['cart'] ทิ้งไปเลย
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }

        // 6. ตั้งข้อความแจ้งเตือน (Success)
        $_SESSION['message'] = "Removed '" . htmlspecialchars($removed_item_name) . "' from your cart.";
        $_SESSION['message_type'] = "success"; // หรือ 'info'

    } else {
        // ไม่มี key นี้ในตะกร้า (อาจจะกดซ้ำ หรือเข้ามาผิด)
        $_SESSION['message'] = "Item not found in cart.";
        $_SESSION['message_type'] = "warning";
    }
} else {
    // ไม่ได้ส่ง key มาทาง URL
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
}

// 7. ไม่ว่าจะสำเร็จหรือไม่ ให้ส่งกลับไปหน้าตะกร้าเสมอ
header("Location: cart.php");
exit();
?>