<?php
session_start();
include 'connectdb.php'; // <-- 1. ใช้ connectdb.php

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    body { background-color: #f8f9fa; }
    .navbar { min-height: 80px; }
    .table-responsive .table img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid" style="max-width: 1600px;">
      <a class="navbar-brand" href="dashboard.php" style="font-size: 1.5rem;">
        <i class="fa-solid fa-shield-halved"></i> KEYVERSE Admin Panel
      </a>
      <div class="collapse navbar-collapse" id="adminNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
          <li class="nav-item"><a class="nav-link" href="index.php" target="_blank"><i class="fa-solid fa-eye"></i> View Site</a></li>
          <li class="nav-item"><span class="nav-link active">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span></li>
          <li class="nav-item"><a class="btn btn-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container" style="max-width: 1600px; margin-top: 30px;">
    
    <?php
      if (isset($_SESSION['message'])) {
        $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'danger';
        echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">
                ' . $_SESSION['message'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
      }
    ?>

    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="true">
          <i class="fa-solid fa-box-archive"></i> จัดการสินค้า (Stock)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
          <i class="fa-solid fa-truck-fast"></i> จัดการคำสั่งซื้อ (New)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
          <i class="fa-solid fa-history"></i> ประวัติการสั่งซื้อ (All)
        </button>
      </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">

      <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
        <div class="card card-body border-top-0">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>รายการสินค้าทั้งหมด</h3>
            <a href="add_product.php" class="btn btn-success">
              <i class="fa-solid fa-plus"></i> เพิ่มสินค้าใหม่
            </a>
          </div>
          
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>รูปภาพ</th>
                  <th>ชื่อสินค้า</th>
                  <th>ราคา</th>
                  <th>ประเภท</th>
                  <th>ขนาด</th> <th>สวิตซ์</th>
                  <th>สต็อก</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  // 4. ดึง keyboard_size มาด้วย
                  $result = $conn->query("SELECT product_id, image_url, name, price, category, keyboard_size, available_switches, stock_quantity FROM products ORDER BY product_id ASC");
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $row['product_id'] . "</td>";
                      if (!empty($row['image_url']) && file_exists($row['image_url'])) {
                        echo "<td><img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['name']) . "'></td>";
                      } else {
                        echo "<td><img src='img/placeholder.png' alt='No Image'></td>"; 
                      }
                      echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                      echo "<td>" . number_format($row['price'], 2) . "</td>";
                      echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['keyboard_size']) . "</td>"; // <-- 5. แสดงผล
                      echo "<td>" . htmlspecialchars($row['available_switches']) . "</td>";
                      echo "<td>" . $row['stock_quantity'] . "</td>";
                      echo "<td>
                              <div class='btn-group' role='group' aria-label='Product Actions'>
                                <a href='edit_product.php?id=" . $row['product_id'] . "' class='btn btn-sm btn-primary'><i class='fa-solid fa-pen'></i> แก้ไข</a>
                                <a href='delete_product.php?id=" . $row['product_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?\");'><i class='fa-solid fa-trash'></i> ลบ</a>
                              </div>
                            </td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='9' class='text-center'>ยังไม่มีสินค้าในระบบ</td></tr>"; // <-- 6. แก้ colspan="9"
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
        <div class="card card-body border-top-0">
          <h3>คำสั่งซื้อที่รอจัดการ (Pending / Processing)</h3>
          <table class="table table-hover align-middle">
            <thead class="table-warning">
              <tr>
                <th>Order ID</th>
                <th>วันที่</th>
                <th>User ID</th>
                <th>ยอดรวม</th>
                <th>สถานะ</th>
                <th>จัดการ (Actions)</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // ดึงข้อมูลจากตาราง orders
                $order_result = $conn->query("SELECT order_id, user_id, DATE_FORMAT(order_date, '%Y-%m-%d %H:%i') as order_time, total_amount, status FROM orders WHERE status = 'Pending' OR status = 'Processing' ORDER BY order_date ASC");
                if ($order_result->num_rows > 0) {
                  while ($row = $order_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['order_id'] . "</td>";
                    echo "<td>" . $row['order_time'] . "</td>";
                    echo "<td>" . $row['user_id'] . "</td>";
                    echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
                    
                    if ($row['status'] == 'Pending') {
                      echo "<td><span class='badge bg-warning text-dark'>Pending</span></td>";
                    } else {
                      echo "<td><span class='badge bg-info text-dark'>Processing</span></td>";
                    }
                    
                    // ปุ่ม Actions
                    echo "<td>
                            <div class='btn-group'>
                              <a href='view_order.php?id=" . $row['order_id'] . "' class='btn btn-sm btn-info'><i class='fa-solid fa-search'></i> ดูรายละเอียด</a>";
                    
                    if ($row['status'] == 'Pending') {
                      echo "<a href='update_order_status.php?id=" . $row['order_id'] . "&status=Processing' class='btn btn-sm btn-success' onclick='return confirm(\"ยืนยันรับออเดอร์นี้?\")'><i class='fa-solid fa-check'></i> ยืนยัน</a>";
                    } 
                    elseif ($row['status'] == 'Processing') {
                      echo "<a href='update_order_status.php?id=" . $row['order_id'] . "&status=Shipped' class='btn btn-sm btn-primary' onclick='return confirm(\"ยืนยันส่งของออเดอร์นี้?\")'><i class='fa-solid fa-truck'></i> ส่งของ</a>";
                    }
                    
                    echo "  </div>
                          </td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='6' class='text-center'>ไม่มีคำสั่งซื้อที่รอจัดการ</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="card card-body border-top-0">
          <h3>ประวัติการสั่งซื้อทั้งหมด</h3>
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>วันที่</th>
                <th>User ID</th>
                <th>ยอดรวม</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
              </tr>
            </thead>
            <tbody>
             <?php
                // ดึงข้อมูลจากตาราง orders
                $history_result = $conn->query("SELECT order_id, user_id, DATE_FORMAT(order_date, '%Y-%m-%d %H:%i') as order_time, total_amount, status FROM orders ORDER BY order_date DESC");
                if ($history_result->num_rows > 0) {
                  while ($row = $history_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['order_id'] . "</td>";
                    echo "<td>" . $row['order_time'] . "</td>";
                    echo "<td>" . $row['user_id'] . "</td>";
                    echo "<td>" . number_format($row['total_amount'], 2) . "</td>";
                    // แสดง Badge ตามสถานะ
                    if ($row['status'] == 'Shipped' || $row['status'] == 'Completed') {
                      echo "<td><span class='badge bg-success'>Completed</span></td>";
                    } elseif ($row['status'] == 'Cancelled') {
                      echo "<td><span class='badge bg-danger'>Cancelled</span></td>";
                    } elseif ($row['status'] == 'Processing') {
                      echo "<td><span class='badge bg-info text-dark'>Processing</span></td>";
                    } elseif ($row['status'] == 'Pending') {
                      echo "<td><span class='badge bg-warning text-dark'>Pending</span></td>";
                    } else {
                       echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($row['status']) . "</span></td>";
                    }
                    echo "<td>
                            <a href='view_order.php?id=" . $row['order_id'] . "' class='btn btn-sm btn-info'><i class='fa-solid fa-search'></i> ดูรายละเอียด</a>
                          </td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='6' class='text-center'>ยังไม่มีประวัติการสั่งซื้อ</td></tr>";
                }
                $conn->close(); // ปิดการเชื่อมต่อ DB
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>