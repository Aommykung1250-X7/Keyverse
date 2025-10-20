<?php 
session_start(); 
// ไม่ต้อง include connectdb.php ถ้าแค่แสดงข้อความ
// แต่ถ้าต้องการดึงข้อมูลออเดอร์มาแสดงเพิ่ม ก็ต้อง include ครับ

// ดึง Order ID จาก URL
$order_id = $_GET['order_id'] ?? null; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thank You for Your Order! - KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    /* (CSS Navbar ... เหมือนเดิม) */
    .navbar .container-fluid { max-width: 1600px; /*...*/ }
    .navbar { min-height: 80px; /*...*/ }
    /* ... */
    body { color: #4d4c51; background-color: #f8f9fa; } 
    /* ... */
    .thank-you-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 40px;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        text-align: center;
    }
    .thank-you-icon {
        font-size: 4rem;
        color: #198754; /* สีเขียว Success */
        margin-bottom: 20px;
    }
  </style>
  
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary rounded mb-4" aria-label="Thirteenth navbar example">
     <div class="container-fluid"> <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11"> <div class="d-lg-flex col-lg-3 justify-content-lg-end align-items-center"> <?php $cart_count = 0; if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) { foreach ($_SESSION['cart'] as $item) { $cart_count += $item['quantity']; } } ?> <a href="cart.php" id="cart-icon-nav" class="btn btn-link p-2 position-relative" style="box-shadow: none" aria-label="Cart"> <svg></svg> <?php if ($cart_count > 0): ?> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_count; ?><span class="visually-hidden">items in cart</span></span> <?php endif; ?> </a> <?php if (isset($_SESSION['user_id'])): ?> <div class="nav-item dropdown"></div> <?php else: ?> <a href="login.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Login/Register"><svg></svg></a> <?php endif; ?> </div> </div> </div>
  </nav>

  <div class="container">
      <div class="thank-you-container">
          <div class="thank-you-icon">
              <i class="fas fa-check-circle"></i>
          </div>
          <h1 class="display-6">Thank You!</h1>
          
          <?php
            if (isset($_SESSION['message']) && $_SESSION['message_type'] === 'success') {
              echo '<p class="lead mt-3">' . htmlspecialchars($_SESSION['message']) . '</p>';
              // ไม่ต้อง unset เพราะหน้านี้แสดงแค่ครั้งเดียวหลังสั่งซื้อ
              // แต่ถ้าต้องการให้หายไปเมื่อรีเฟรช ก็ unset ได้
              // unset($_SESSION['message']);
              // unset($_SESSION['message_type']);
            } else {
               echo '<p class="lead mt-3">Your order has been placed successfully.</p>'; // ข้อความสำรอง
            }
          ?>

          <?php if ($order_id): ?>
            <p>Your Order ID is: <strong>#<?php echo htmlspecialchars($order_id); ?></strong></p>
            <p class="text-muted">You can track your order status in the <a href="orders.php">My Orders</a> section.</p>
          <?php endif; ?>
          
          <hr class="my-4">
          
          <a href="store.php" class="btn btn-primary">Continue Shopping</a>
          <a href="index.php" class="btn btn-outline-secondary">Go to Homepage</a>
      </div>
  </div> 

  <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> </footer></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
</body>
</html>