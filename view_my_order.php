<?php
session_start();
include 'connectdb.php'; // ใช้ connectdb.php

// --- การป้องกัน ---
// 1. ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your order details.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id']; // เก็บ User ID ไว้ใช้ตรวจสอบ

// 2. ตรวจสอบว่ามี ID ของออเดอร์ส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Order ID not specified.";
    $_SESSION['message_type'] = "danger";
    header("Location: orders.php"); // กลับไปหน้า My Orders
    exit();
}
$order_id = $_GET['id'];

// 3. ดึงข้อมูลออเดอร์หลัก (เหมือนเดิม แต่จะเช็ค user_id ทีหลัง)
$stmt = $conn->prepare("SELECT o.*, u.username 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.user_id
                        WHERE o.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    $_SESSION['message'] = "Order not found (ID: " . $order_id . ")";
    $_SESSION['message_type'] = "danger";
    $stmt->close();
    $conn->close();
    header("Location: orders.php"); // กลับไปหน้า My Orders
    exit();
}
$order = $order_result->fetch_assoc();
$stmt->close();

// *** 4. ตรวจสอบความเป็นเจ้าของออเดอร์ ***
if ($order['user_id'] != $user_id) {
    $_SESSION['message'] = "You do not have permission to view this order.";
    $_SESSION['message_type'] = "danger";
    $conn->close();
    header("Location: orders.php"); // กลับไปหน้า My Orders
    exit();
}
// *** จบการตรวจสอบ ***

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Details #<?php echo $order['order_id']; ?> - KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style> 
    .navbar .container-fluid { max-width: 1600px; width: 100%; margin-left: auto; margin-right: auto; }
    .navbar { min-height: 80px; font-size: 1.25rem; }
    .nav-item { font-size: medium; padding-left: 16px; padding-right: 16px; }
    @media (max-width: 991.98px) { .nav-item { padding-left: 8px; padding-right: 8px; font-size: 0.95rem; } }
    .navbar .navbar-brand { font-size: 2rem; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .navbar .nav-link { padding-top: 1rem; padding-bottom: 1rem; }
    .navbar .nav-link.active, .navbar .nav-link:focus { color: #000 !important; font-weight: bold; } 
    .btn-link svg { width: 30px !important; height: 30px !important; }
    body { color: #4d4c51; background-color: #f8f9fa; } /* เปลี่ยนสีพื้นหลังเล็กน้อย */
    .navbar .dropdown-toggle { color: #4d4c51; font-weight: 400; font-size: 1.1rem; }
    .navbar .dropdown-menu { font-size: 1rem; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary rounded" aria-label="Thirteenth navbar example">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample11"
        aria-controls="navbarsExample11" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11">
        <a class="navbar-brand col-lg-3 me-0" style="padding-left: 18px; color: #4d4c51" href="index.php">KEYVERSE</a> <ul class="navbar-nav col-lg-6 justify-content-lg-center">
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="store.php">Store</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="keyboard.php">Keyboards</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="switches.php">Switches</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="keycap.php">Keycaps</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="accessories.php">Accessories</a>
          </li>
          
        </ul>
        <div class="d-lg-flex col-lg-3 justify-content-lg-end align-items-center">
          
          <?php
            $cart_count = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) { $cart_count += $item['quantity']; }
            }
          ?>
          <a href="cart.php" id="cart-icon-nav" class="btn btn-link p-2 position-relative" style="box-shadow: none" aria-label="Cart">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.125A4.125 4.125 0 008.25 7.125V10.5M3.375 9.75l1.125 9A2.25 2.25 0 006.75 21h10.5a2.25 2.25 0 002.25-2.25l1.125-9a.75.75 0 00-.75-.75H4.125a.75.75 0 00-.75.75z" /></svg>
            <?php if ($cart_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $cart_count; ?><span class="visually-hidden">items in cart</span>
              </span>
            <?php endif; ?>
          </a>

          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                  <li><a class="dropdown-item" href="dashboard.php">Admin Dashboard</a></li>
                  <li><hr class="dropdown-divider"></li>
                <?php endif; ?>
              
                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="login.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Login/Register">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A1.875 1.875 0 0118.375 22.5H5.625a1.875 1.875 0 01-1.124-2.382z" /></svg>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <div class="container" style="max-width: 1200px; margin-top: 30px;">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Order Details </h3>
        <a href="orders.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to My Orders</a>
      </div>
      
      <div class="card-body">
        <div class="row g-4 mb-4">
          <div class="col-md-4">
            <h5>Customer Info</h5>
            <strong>User ID:</strong> <?php echo $order['user_id']; ?><br>
            <strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?>
          </div>
          <div class="col-md-4">
            <h5>Order Info</h5>
            <strong>Order Date:</strong> <?php echo date("d M Y, H:i", strtotime($order['order_date'])); ?><br>
            <strong>Total Amount:</strong> <?php echo number_format($order['total_amount'], 2); ?> THB
          </div>
          <div class="col-md-4">
            <h5>Current Status</h5>
            <?php 
              $status_class = 'bg-secondary'; // Default
              if ($order['status'] == 'Completed' || $order['status'] == 'Shipped') { $status_class = 'bg-success'; } 
              elseif ($order['status'] == 'Processing') { $status_class = 'bg-info text-dark'; } 
              elseif ($order['status'] == 'Pending') { $status_class = 'bg-warning text-dark'; } 
              elseif ($order['status'] == 'Cancelled') { $status_class = 'bg-danger'; }
            ?>
            <span class="badge fs-6 <?php echo $status_class; ?>">
                <?php echo htmlspecialchars($order['status']); ?>
            </span>
          </div>
          <div class="col-12">
            <h5>Shipping Address</h5>
            <pre><?php echo htmlspecialchars($order['shipping_address']); ?></pre>
          </div>
        </div>

        <hr>

        <h4 class="mb-3">Items in this Order</h4>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Image</th>
                <th>Product (ID)</th>
                <th>Switch (if any)</th>
                <th>Price/Unit</th>
                <th>Qty</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // ดึงข้อมูล "ของ" ในออเดอร์
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
                  <td><img src="<?php echo htmlspecialchars($item['image_url'] ?? 'img/placeholder.png'); ?>" width="60" height="60" style="object-fit: cover; border-radius: 5px;" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                  <td><strong><?php echo htmlspecialchars($item['name']); ?></strong><br><small class="text-muted">PID: <?php echo $item['product_id']; ?></small></td>
                  <td><?php echo htmlspecialchars($item['selected_switch'] ?? 'N/A'); ?></td>
                  <td><?php echo number_format($item['price_per_unit'], 2); ?></td>
                  <td><?php echo $item['quantity']; ?></td>
                  <td><?php echo number_format($item['price_per_unit'] * $item['quantity'], 2); ?></td>
                </tr>
              <?php
                  }
                } else {
                  echo "<tr><td colspan='6' class='text-center'>No items found for this order.</td></tr>";
                }
                $items_stmt->close();
              ?>
            </tbody>
          </table>
        </div>
        
        </div> </div> </div> <?php $conn->close(); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>