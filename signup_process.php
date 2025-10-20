<?php
session_start();
include 'connectdb.php'; 

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password']; // **รับรหัสผ่านแบบธรรมดา**
$confirm_password = $_POST['confirm_password'];

// 1. ตรวจสอบว่ารหัสผ่านตรงกัน
if ($password !== $confirm_password) {
    $_SESSION['message'] = "รหัสผ่านทั้งสองช่องไม่ตรงกัน!";
    $_SESSION['message_type'] = "error";
    header("Location: signup.php"); 
    exit();
}

// 2. ตรวจสอบ username หรือ email ซ้ำ
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['message'] = "Username หรือ Email นี้มีผู้ใช้งานแล้ว!";
    $_SESSION['message_type'] = "error";
    $stmt->close();
    $conn->close();
    header("Location: signup.php");
    exit();
}
$stmt->close();

// 3. กำหนดค่า role เป็น 'user'
$role = 'user'; // **กำหนด role เป็น 'user' เสมอสำหรับหน้าสมัคร**

// 4. บันทึกข้อมูลลง Database (!! ไม่มีการ hash รหัสผ่าน !!)
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
// **เปลี่ยน bind_param เป็น "ssss" (string, string, string, string)**
$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    // สมัครสมาชิกสำเร็จ
    $_SESSION['message'] = "สมัครสมาชิกสำเร็จ! กรุณาล็อกอิน";
    $_SESSION['message_type'] = "success";
    $stmt->close();
    $conn->close();
    header("Location: login.php"); // ส่งไปหน้า login
    exit();
} else {
    // มีปัญหาในการบันทึก
    $_SESSION['message'] = "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $conn->error;
    $_SESSION['message_type'] = "error";
    $stmt->close();
    $conn->close();
    header("Location: signup.php");
    exit();
}
?>