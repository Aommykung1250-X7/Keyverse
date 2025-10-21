<?php
session_start();
include 'connectdb.php';

// --- การป้องกันเบื้องต้น ---
if (!isset($_SESSION['user_id'])) { /* ... redirect to login ... */ exit(); }
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) { /* ... redirect to cart ... */ exit(); }

// --- คำนวณยอดรวม ---
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) { $subtotal += $item['price'] * $item['quantity']; }
$shipping_cost = 0.00; // ค่าส่งเป็น 0 (ตามโค้ดเดิม)
$grand_total = $subtotal;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout - KEYVERSE</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    /* (CSS Navbar ... เหมือนเดิม) */
    .navbar .container-fluid { max-width: 1600px; width: 100%; margin-left: auto; margin-right: auto; }
    .navbar { min-height: 80px; font-size: 1.25rem; }
    .nav-item { font-size: medium; padding-left: 16px; padding-right: 16px; }
    @media (max-width: 991.98px) { .nav-item { padding-left: 8px; padding-right: 8px; font-size: 0.95rem; } }
    .navbar .navbar-brand { font-size: 2rem; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .navbar .nav-link { padding-top: 1rem; padding-bottom: 1rem; }
    .navbar .nav-link.active, .navbar .nav-link:focus { color: #000 !important; font-weight: bold; }
    .btn-link svg { width: 30px !important; height: 30px !important; }
    body { color: #4d4c51; background-color: #f8f9fa; }
    .navbar .dropdown-toggle { color: #4d4c51; font-weight: 400; font-size: 1.1rem; }
    .navbar .dropdown-menu { font-size: 1rem; }

    /* (CSS Checkout Page ... เหมือนเดิม) */
    .order-summary-card { background-color: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #eee; }
    .summary-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px; }
    /* เพิ่มเพื่อให้แน่ใจว่า Badge อยู่ถูกที่ */
    .navbar .position-relative { display: inline-block; /* หรือ inline-flex */ } 
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

  <div class="container my-5">
    <h2>Checkout</h2>
    <hr class="mb-4">

     <?php if (isset($_SESSION['message'])) { /* ... */ } ?>

    <form action="place_order_process.php" method="POST">
      <div class="row g-5">
        <div class="col-md-7 col-lg-8">
          <h4 class="mb-3">Shipping Address</h4>
          <div class="row g-3">
             <div class="col-sm-6"> <label for="firstName" class="form-label">First name</label> <input type="text" class="form-control" id="firstName" name="first_name" required> </div>
             <div class="col-sm-6"> <label for="lastName" class="form-label">Last name</label> <input type="text" class="form-control" id="lastName" name="last_name" required> </div>
             <div class="col-12"> <label for="phone" class="form-label">Phone Number</label> <input type="tel" class="form-control" id="phone" name="phone" placeholder="08x-xxx-xxxx" required> </div>
             <div class="col-12"> <label for="address" class="form-label">Address</label> <input type="text" class="form-control" id="address" name="address1" placeholder="1234 Main St" required> </div>
             <div class="col-12"> <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label> <input type="text" class="form-control" id="address2" name="address2"> </div>
             <div class="col-md-5"> <label for="city" class="form-label">City / District</label> <input type="text" class="form-control" id="city" name="city" required> </div>
             <div class="col-md-4"> <label for="province" class="form-label">Province</label> <input type="text" class="form-control" id="province" name="province" required> </div>
             <div class="col-md-3"> <label for="zip" class="form-label">Zip / Postal Code</label> <input type="text" class="form-control" id="zip" name="zip_code" required> </div>
          </div>
          <hr class="my-4">
          <h4 class="mb-3">Payment Method</h4>
          <div class="my-3">
             <div class="form-check"> <input id="cod" name="paymentMethod" type="radio" class="form-check-input" value="cod" checked required> <label class="form-check-label" for="cod">Cash on Delivery (COD)</label> </div>
             <div class="form-check"> <input id="bankTransfer" name="paymentMethod" type="radio" class="form-check-input" value="bank_transfer" required disabled> <label class="form-check-label" for="bankTransfer">Bank Transfer (Coming Soon)</label> </div>
          </div>
           <hr class="my-4">
           <button class="w-100 btn btn-primary btn-lg add-to-cart-btn" type="submit">Place Order</button>
        </div>
        <div class="col-md-5 col-lg-4 order-md-last">
          <h4 class="d-flex justify-content-between align-items-center mb-3"> <span class="text-primary">Your cart</span> <span class="badge bg-primary rounded-pill"><?php echo $cart_count; ?></span> </h4>
          <div class="order-summary-card p-3">
            <ul class="list-group list-group-flush mb-3">
              <?php foreach ($_SESSION['cart'] as $item): ?>
                <li class="list-group-item d-flex justify-content-between lh-sm summary-item"> <img src="<?php echo (!empty($item['image']) && file_exists($item['image'])) ? htmlspecialchars($item['image']) : 'img/placeholder.png'; ?>" alt=""> <div> <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6> <small class="text-muted"> Qty: <?php echo $item['quantity']; ?> <?php if(!empty($item['switch'])): ?> (<?php echo htmlspecialchars($item['switch']); ?>) <?php endif; ?> </small> </div> <span class="text-muted">฿<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span> </li>
              <?php endforeach; ?>
              <li class="list-group-item d-flex justify-content-between bg-light"> <div class="text-secondary"> <h6 class="my-0">Shipping</h6> <small>Standard Delivery</small> </div> <span class="text-secondary">฿<?php echo number_format($shipping_cost, 2); ?></span> </li>
              <li class="list-group-item d-flex justify-content-between fs-5 fw-bold"> <span>Total (THB)</span> <strong>฿<?php echo number_format($grand_total, 2); ?></strong> </li>
            </ul>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> </footer></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>