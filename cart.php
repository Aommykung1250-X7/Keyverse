<?php 
session_start(); 
include 'connectdb.php'; // อาจจะไม่ต้องใช้ถ้าไม่ดึงข้อมูลอื่นเพิ่ม

// คำนวณยอดรวม
$total_price = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Shopping Cart - KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    /* (CSS ของ Navbar, Dropdown ... เหมือนเดิม) */
    .navbar .container-fluid { max-width: 1600px; width: 100%; margin-left: auto; margin-right: auto; }
    .navbar { min-height: 80px; font-size: 1.25rem; }
    .nav-item { font-size: medium; padding-left: 16px; padding-right: 16px; }
    @media (max-width: 991.98px) { .nav-item { padding-left: 8px; padding-right: 8px; font-size: 0.95rem; } }
    .navbar .navbar-brand { font-size: 2rem; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .navbar .nav-link { padding-top: 1rem; padding-bottom: 1rem; }
    .navbar .nav-link.active, .navbar .nav-link:focus { color: #866953 !important; font-weight: 400; } 
    .btn-link svg { width: 30px !important; height: 30px !important; }
    body { color: #4d4c51; background-color: #f8f9fa; } 
    .navbar .dropdown-toggle { color: #4d4c51; font-weight: 400; font-size: 1.1rem; }
    .navbar .dropdown-menu { font-size: 1rem; }

    /* Cart Page Styles */
    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 15px;
    }
    .cart-item-details {
        flex-grow: 1;
    }
    .cart-item-name {
        font-weight: 500;
        margin-bottom: 5px;
    }
    .cart-item-switch {
        font-size: 0.9em;
        color: #6c757d;
    }
    .remove-btn {
        color: #dc3545;
        text-decoration: none;
        font-size: 0.9em;
    }
    .remove-btn:hover {
        text-decoration: underline;
    }
    .summary-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
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
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="modding.php">DIY / Modding</a>
          </li>
        </ul>
        <div class="d-lg-flex col-lg-3 justify-content-lg-end align-items-center">
          <a href="search.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Search"> <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" /></svg>
          </a>
          
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
                <li><a class="dropdown-item" href="profile.php">My Account</a></li>
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
    <h2>Your Shopping Cart</h2><hr>
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
      <div class="row g-4">
        <div class="col-lg-8">
          <?php foreach ($_SESSION['cart'] as $key => $item): ?>
            <div class="card mb-3 shadow-sm"> <div class="card-body"> <div class="d-flex align-items-center"> <img src="<?php echo (!empty($item['image']) && file_exists($item['image'])) ? htmlspecialchars($item['image']) : 'img/placeholder.png'; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-img"> <div class="cart-item-details"> <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div> <?php if (!empty($item['switch'])): ?> <div class="cart-item-switch">Switch: <?php echo htmlspecialchars($item['switch']); ?></div> <?php endif; ?> <div>Price: ฿<?php echo number_format($item['price'], 2); ?></div> </div> <div class="text-center mx-3" style="min-width: 60px;"> Qty: <?php echo $item['quantity']; ?> </div> <div class="text-end fw-bold mx-3" style="min-width: 100px;"> ฿<?php echo number_format($item['price'] * $item['quantity'], 2); ?> </div> <a href="remove_from_cart.php?key=<?php echo urlencode($key); ?>" class="remove-btn ms-3" title="Remove item" onclick="return confirm('Remove this item from your cart?')"> <i class="fas fa-trash-alt"></i> </a> </div> </div> </div>
          <?php endforeach; ?>
        </div>
        <div class="col-lg-4">
          <div class="summary-card p-4"> <h4>Order Summary</h4><hr> <div class="d-flex justify-content-between mb-3"> <span>Subtotal</span> <span>฿<?php echo number_format($total_price, 2); ?></span> </div> <hr> <div class="d-flex justify-content-between fw-bold fs-5"> <span>Total</span> <span>฿<?php echo number_format($total_price, 2); ?></span> </div> <div class="d-grid gap-2 mt-4"> <a href="checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a> <a href="store.php" class="btn btn-outline-secondary">Continue Shopping</a> </div> </div>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center" role="alert"> Your shopping cart is empty. <a href="login.php" class="alert-link">Please Login!!</a> </div>
    <?php endif; ?>
  </div> 

  <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> </footer></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
</body>
</html>