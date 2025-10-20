<?php 
session_start(); 
include 'connectdb.php'; 

// --- การป้องกัน ---
// 1. ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your orders.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php"); 
    exit();
}
$user_id = $_SESSION['user_id']; // เก็บ User ID ไว้ใช้ query

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Orders - KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    /* (CSS Navbar ... เหมือนเดิม) */
    .navbar .container-fluid { max-width: 1600px; /*...*/ }
    .navbar { min-height: 80px; /*...*/ }
    /* ... */
    body { color: #4d4c51; background-color: #f8f9fa; } 
    /* ... */
    .table th {
        background-color: #f8f9fa; /* สีพื้นหลังหัวตาราง */
    }
    .status-badge {
        font-size: 0.9em;
        padding: 0.4em 0.7em;
    }
  </style>
  
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary rounded mb-4" aria-label="Thirteenth navbar example">
     <div class="container-fluid"> <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11"> <div class="d-lg-flex col-lg-3 justify-content-lg-end align-items-center"> <?php $cart_count = 0; if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) { foreach ($_SESSION['cart'] as $item) { $cart_count += $item['quantity']; } } ?> <a href="cart.php" id="cart-icon-nav" class="btn btn-link p-2 position-relative" style="box-shadow: none" aria-label="Cart"> <svg></svg> <?php if ($cart_count > 0): ?> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_count; ?><span class="visually-hidden">items in cart</span></span> <?php endif; ?> </a> <?php if (isset($_SESSION['user_id'])): ?> <div class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a><ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown"><?php if ($_SESSION['role'] === 'admin'): ?><li><a class="dropdown-item" href="dashboard.php">Admin Dashboard</a></li><li><hr class="dropdown-divider"></li><?php endif; ?><li><a class="dropdown-item" href="profile.php">My Account</a></li><li><a class="dropdown-item active" href="orders.php">My Orders</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li></ul></div> <?php else: ?> <a href="login.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Login/Register"><svg></svg></a> <?php endif; ?> </div> </div> </div>
  </nav>

  <div class="container my-5">
    <h2>My Orders</h2>
    <hr class="mb-4">

    <?php
     if (isset($_SESSION['message'])) {
       $message_type_class = 'alert-danger'; 
       // ... (โค้ดแสดง Alert เหมือนหน้าอื่นๆ) ...
       echo '<div class="alert ' . $message_type_class . ' alert-dismissible fade show" role="alert">'.htmlspecialchars($_SESSION['message']).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
       unset($_SESSION['message']); unset($_SESSION['message_type']);
     }
    ?>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col">Order ID</th>
                <th scope="col">Order Date</th>
                <th scope="col">Total Amount</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // --- ดึงข้อมูลออเดอร์ของ User คนนี้ ---
                $stmt = $conn->prepare("SELECT order_id, DATE_FORMAT(order_date, '%d %b %Y, %H:%i') as formatted_date, total_amount, status 
                                         FROM orders 
                                         WHERE user_id = ? 
                                         ORDER BY order_date DESC"); // เรียงจากล่าสุด
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  while ($order = $result->fetch_assoc()) {
              ?>
                    <tr>
                      <th scope="row">#<?php echo $order['order_id']; ?></th>
                      <td><?php echo $order['formatted_date']; ?></td>
                      <td>฿<?php echo number_format($order['total_amount'], 2); ?></td>
                      <td>
                          <?php 
                          // แสดง Badge ตามสถานะ
                          $status_class = 'bg-secondary'; // Default
                          if ($order['status'] == 'Completed' || $order['status'] == 'Shipped') {
                              $status_class = 'bg-success';
                          } elseif ($order['status'] == 'Processing') {
                              $status_class = 'bg-info text-dark';
                          } elseif ($order['status'] == 'Pending') {
                              $status_class = 'bg-warning text-dark';
                          } elseif ($order['status'] == 'Cancelled') {
                              $status_class = 'bg-danger';
                          }
                          ?>
                          <span class="badge status-badge <?php echo $status_class; ?>">
                              <?php echo htmlspecialchars($order['status']); ?>
                          </span>
                      </td>
                      <td>
                          <a href="view_my_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                              <i class="fas fa-eye me-1"></i> View Details
                          </a>
                          </td>
                    </tr>
              <?php
                  } // end while
                } else {
                  // ถ้าไม่มีออเดอร์
                  echo '<tr><td colspan="5" class="text-center text-muted py-4">You have not placed any orders yet.</td></tr>';
                }
                $stmt->close();
                $conn->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> 

  <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> </footer></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
</body>
</html>