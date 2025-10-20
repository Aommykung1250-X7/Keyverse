<?php 
session_start(); 
include 'connectdb.php'; 

// --- การป้องกันเบื้องต้น ---
// 1. ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to proceed to checkout.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php"); // ส่งไปหน้า login
    exit();
}

// 2. ตรวจสอบว่าตะกร้าว่างหรือไม่
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty. Please add items before checking out.";
    $_SESSION['message_type'] = "info";
    header("Location: cart.php"); // ส่งกลับไปหน้าตะกร้า
    exit();
}

// --- คำนวณยอดรวม (เหมือนใน cart.php) ---
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_cost = 0.00; // ตั้งค่าส่งเป็น 0 เพื่อไม่ให้รวมในยอดรวม
$grand_total = $subtotal; // ไม่บวกค่าจัดส่ง

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
    .navbar .container-fluid { max-width: 1600px; /*...*/ }
    .navbar { min-height: 80px; /*...*/ }
    /* ... */
    body { color: #4d4c51; background-color: #f8f9fa; } 
    /* ... */

    /* Checkout Page Styles */
    .order-summary-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
     .summary-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 10px;
    }
  </style>
  
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary rounded mb-4" aria-label="Thirteenth navbar example">
     <div class="container-fluid"> <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11"> <div class="d-lg-flex col-lg-3 justify-content-lg-end align-items-center"> <?php $cart_count = 0; if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) { foreach ($_SESSION['cart'] as $item) { $cart_count += $item['quantity']; } } ?> <a href="cart.php" id="cart-icon-nav" class="btn btn-link p-2 position-relative" style="box-shadow: none" aria-label="Cart"> <svg></svg> <?php if ($cart_count > 0): ?> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_count; ?><span class="visually-hidden">items in cart</span></span> <?php endif; ?> </a> <?php if (isset($_SESSION['user_id'])): ?> <div class="nav-item dropdown"></div> <?php else: ?> <a href="login.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Login/Register"><svg></svg></a> <?php endif; ?> </div> </div> </div>
  </nav>

  <div class="container my-5">
    <h2>Checkout</h2>
    <hr class="mb-4">

     <?php
      if (isset($_SESSION['message'])) {
        $message_type_class = 'alert-danger'; 
        // ... (โค้ดแสดง Alert เหมือนหน้าอื่นๆ) ...
        echo '<div class="alert ' . $message_type_class . ' alert-dismissible fade show" role="alert">'.htmlspecialchars($_SESSION['message']).'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['message']); unset($_SESSION['message_type']);
      }
     ?>

    <form action="place_order_process.php" method="POST">
      <div class="row g-5">
        
        <div class="col-md-7 col-lg-8">
          <h4 class="mb-3">Shipping Address</h4>
          
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="firstName" class="form-label">First name</label>
              <input type="text" class="form-control" id="firstName" name="first_name" placeholder="" value="" required>
              <div class="invalid-feedback"> Valid first name is required. </div>
            </div>

            <div class="col-sm-6">
              <label for="lastName" class="form-label">Last name</label>
              <input type="text" class="form-control" id="lastName" name="last_name" placeholder="" value="" required>
              <div class="invalid-feedback"> Valid last name is required. </div>
            </div>

            <div class="col-12">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="08x-xxx-xxxx" required>
               <div class="invalid-feedback"> Please enter a valid phone number. </div>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" id="address" name="address1" placeholder="1234 Main St" required>
              <div class="invalid-feedback"> Please enter your shipping address. </div>
            </div>

            <div class="col-12">
              <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label>
              <input type="text" class="form-control" id="address2" name="address2" placeholder="Apartment or suite">
            </div>

            <div class="col-md-5">
              <label for="city" class="form-label">City / District</label>
              <input type="text" class="form-control" id="city" name="city" placeholder="" required>
               <div class="invalid-feedback"> City required. </div>
            </div>

            <div class="col-md-4">
              <label for="province" class="form-label">Province</label>
               <input type="text" class="form-control" id="province" name="province" placeholder="" required>
              <div class="invalid-feedback"> Province required. </div>
            </div>

            <div class="col-md-3">
              <label for="zip" class="form-label">Zip / Postal Code</label>
              <input type="text" class="form-control" id="zip" name="zip_code" placeholder="" required>
              <div class="invalid-feedback"> Zip code required. </div>
            </div>
          </div>

          <hr class="my-4">

          <h4 class="mb-3">Payment Method</h4>

          <div class="my-3">
            <div class="form-check">
              <input id="cod" name="paymentMethod" type="radio" class="form-check-input" value="cod" checked required>
              <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
            </div>
            <div class="form-check">
              <input id="bankTransfer" name="paymentMethod" type="radio" class="form-check-input" value="bank_transfer" required disabled> 
              <label class="form-check-label" for="bankTransfer">Bank Transfer (Coming Soon)</label>
            </div>
          </div>
          
           <hr class="my-4">
           
           <button class="w-100 btn btn-primary btn-lg add-to-cart-btn" type="submit">Place Order</button>


        </div>

        <div class="col-md-5 col-lg-4 order-md-last">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-primary">Your cart</span>
            <span class="badge bg-primary rounded-pill"><?php echo $cart_count; ?></span>
          </h4>
          <div class="order-summary-card p-3">
            <ul class="list-group list-group-flush mb-3">
              <?php foreach ($_SESSION['cart'] as $item): ?>
                <li class="list-group-item d-flex justify-content-between lh-sm summary-item">
                  <img src="<?php echo (!empty($item['image']) && file_exists($item['image'])) ? htmlspecialchars($item['image']) : 'img/placeholder.png'; ?>" alt="">
                  <div>
                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                    <small class="text-muted">
                        Qty: <?php echo $item['quantity']; ?>
                        <?php if(!empty($item['switch'])): ?>
                           (<?php echo htmlspecialchars($item['switch']); ?>)
                        <?php endif; ?>
                    </small>
                  </div>
                  <span class="text-muted">฿<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </li>
              <?php endforeach; ?>
              
              <li class="list-group-item d-flex justify-content-between bg-light">
                <div class="text-secondary">
                  <h6 class="my-0">Shipping</h6>
                  <small>Standard Delivery</small>
                </div>
                <span class="text-secondary">฿<?php echo number_format($shipping_cost, 2); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between fs-5 fw-bold">
                <span>Total (THB)</span>
                <strong>฿<?php echo number_format($grand_total, 2); ?></strong>
              </li>
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