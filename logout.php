<?php
// 1. เริ่ม session ก่อนเสมอ
session_start();

// 2. ล้างค่า session ทั้งหมดใน array
$_SESSION = array();

// 3. ทำลาย session ที่เซิร์ฟเวอร์
session_destroy();

// 4. ส่งผู้ใช้กลับไปหน้า login.php
header("Location: login.php");
exit(); // สั่งหยุดการทำงานของสคริปต์ทันที
?>