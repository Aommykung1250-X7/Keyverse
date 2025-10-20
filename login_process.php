<?php
session_start(); 
include 'connectdb.php'; 

$username = $_POST['username'];
$password = $_POST['password']; // **รับรหัสผ่านแบบธรรมดา**

// 1. ค้นหา user และดึง role, password (แบบธรรมดา)
// **เปลี่ยน SELECT query ให้ดึง password และ role**
$stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 2. ถ้าเจอ user
    $user = $result->fetch_assoc();
    
    // 3. ตรวจสอบรหัสผ่านแบบธรรมดา (!! ไม่ปลอดภัย !!)
    // **เปลี่ยนจาก password_verify() เป็นการเปรียบเทียบ string ธรรมดา**
    if ($password === $user['password']) {
        // 4. ถ้า!! รหัสผ่านถูกต้อง: สร้าง Session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // **เก็บ role ลง session**
        
        $stmt->close();
        $conn->close();

        // 5. **!! ตรรกะการเปลี่ยนหน้าตาม ROLE !!**
        if ($user['role'] === 'admin') {
            // ถ้าเป็น admin ให้ไปหน้า dashboard
            header("Location: dashboard.php"); 
        } else {
            // ถ้าเป็น user (หรือ role อื่นๆ) ให้ไปหน้า index
            // **แนะนำให้เปลี่ยน index.html เป็น index.php**
            header("Location: index.php"); 
        }
        exit();

    } else {
        // 4. ถ้า!! รหัสผ่านไม่ถูกต้อง
        $_SESSION['message'] = "รหัสผ่านไม่ถูกต้อง!";
        $_SESSION['message_type'] = "error";
    }
} else {
    // 2. ถ้าไม่เจอ user
    $_SESSION['message'] = "ไม่พบผู้ใช้งานนี้ในระบบ!";
    $_SESSION['message_type'] = "error";
}

// 5. ถ้าเกิดข้อผิดพลาด (user ไม่มี, pass ผิด) ให้ส่งกลับไปหน้า login
$stmt->close();
$conn->close();
header("Location: login.php");
exit();
?>