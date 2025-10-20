<?php
session_start();
include 'connectdb.php'; // ใช้ connectdb.php

// 1. ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. ตรวจสอบว่ามี ID ของออเดอร์ส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ไม่ได้ระบุ Order ID";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}
$order_id = $_GET['id']; // <-- ตัวแปร $order_id ถูกต้องแล้ว

// 3. ดึงข้อมูลออเดอร์หลัก (จากตาราง orders)
$stmt = $conn->prepare("SELECT o.*, u.username 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.user_id
                        WHERE o.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    $_SESSION['message'] = "ไม่พบออเดอร์ ID: " . $order_id;
    $_SESSION['message_type'] = "danger";
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php");
    exit();
}
$order = $order_result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Order #<?php echo $order['order_id']; ?> - KEYVERSE Admin</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style> body { background-color: #f8f9fa; } </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid" style="max-width: 1600px;">
      <a class="navbar-brand" href="dashboard.php" style="font-size: 1.5rem;">
        <i class="fa-solid fa-shield-halved"></i> KEYVERSE Admin Panel
      </a>
    </div>
  </nav>

  <div class="container" style="max-width: 1200px; margin-top: 30px;">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3>รายละเอียดออเดอร์ #<?php echo $order['order_id']; ?></h3>
        <a href="dashboard.php#orders" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> กลับไปหน้า Dashboard</a>
      </div>
      
      <div class="card-body">
        <div class="row g-4 mb-4">
          <div class="col-md-4">
            <h5>ข้อมูลลูกค้า</h5>
            <strong>User ID:</strong> <?php echo $order['user_id'] ?? 'N/A'; ?><br>
            <strong>Username:</strong> <?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?>
          </div>
          <div class="col-md-4">
            <h5>ข้อมูลการสั่งซื้อ</h5>
            <strong>วันที่สั่ง:</strong> <?php echo date("d M Y, H:i", strtotime($order['order_date'])); ?><br>
            <strong>ยอดรวม:</strong> <?php echo number_format($order['total_amount'], 2); ?> บาท
          </div>
          <div class="col-md-4">
            <h5>สถานะปัจจุบัน</h5>
            <span class="badge fs-6 bg-primary"><?php echo htmlspecialchars($order['status']); ?></span>
          </div>
          <div class="col-12">
            <h5>ที่อยู่จัดส่ง</h5>
            <pre><?php echo htmlspecialchars($order['shipping_address'] ?? 'N/A'); ?></pre>
          </div>
        </div>

        <hr>

        <h4 class="mb-3">รายการสินค้าในออเดอร์</h4>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>รูปภาพ</th>
                <th>ชื่อสินค้า (Product ID)</th>
                <th>สวิตช์ (ถ้ามี)</th>
                <th>ราคาต่อชิ้น</th>
                <th>จำนวน</th>
                <th>ราคารวม</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $items_stmt = $conn->prepare("SELECT oi.*, p.name, p.image_url 
                                            FROM order_items oi
                                            JOIN products p ON oi.product_id = p.product_id
                                            WHERE oi.order_id = ?");
                $items_stmt->bind_param("i", $order_id);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();

                if ($items_result->num_rows > 0) {
                  while ($item = $items_result->fetch_assoc()) {
              ?>
                <tr>
                  <td>
                    <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'img/placeholder.png'); ?>" 
                         width="60" height="60" style="object-fit: cover; border-radius: 5px;" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                  </td>
                  <td>
                    <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                    <small class="text-muted">PID: <?php echo $item['product_id']; ?></small>
                  </td>
                  <td>
                    <?php echo htmlspecialchars($item['selected_switch'] ?? 'N/A'); ?>
                  </td>
                  <td><?php echo number_format($item['price_per_unit'], 2); ?></td>
                  <td><?php echo $item['quantity']; ?></td>
                  <td><?php echo number_format($item['price_per_unit'] * $item['quantity'], 2); ?></td>
                </tr>
              <?php
                  }
                } else {
                  echo "<tr><td colspan='6' class='text-center'>ไม่พบรายการสินค้าในออเดอร์นี้</td></tr>";
                }
                $items_stmt->close();
              ?>
            </tbody>
          </table>
        </div>
        
        <hr>
        
        <div class="text-end">
          <h5 class="d-inline me-3">อัปเดตสถานะ:</h5>
          
          <a href="update_order_status.php?id=<?php echo $order_id; ?>&status=Processing" class="btn btn-info" 
             onclick='return confirm("ยืนยันการรับออเดอร์ (Processing)?")'>
            <i class="fa-solid fa-cogs"></i> ยืนยัน (Processing)
          </a>
          
          <a href="update_order_status.php?id=<?php echo $order_id; ?>&status=Shipped" class="btn btn-primary"
             onclick='return confirm("ยืนยันการส่งของ (Shipped)?")'>
            <i class="fa-solid fa-truck"></i> ส่งของแล้ว (Shipped)
          </a>
          
          <a href="update_order_status.php?id=<?php echo $order_id; ?>&status=Completed" class="btn btn-success"
             onclick='return confirm("ยืนยันออเดอร์เสร็จสิ้น (Completed)?")'>
            <i class="fa-solid fa-check-circle"></i> เสร็จสิ้น (Completed)
          </a>

          <a href="update_order_status.php?id=<?php echo $order_id; ?>&status=Cancelled" class="btn btn-danger"
             onclick='return confirm("!! แน่ใจหรือไม่ว่าจะ \"ยกเลิก\" ออเดอร์นี้ !!")'>
             <i class="fa-solid fa-times-circle"></i> ยกเลิก (Cancelled)
          </a>
        </div>

      </div>
    </div>
  </div>

  <?php $conn->close(); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>