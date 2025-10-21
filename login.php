<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KEYVERSE - Log In</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    /* ... (โค้ด CSS ของคุณทั้งหมด) ... */
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
    .login-section { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 0; }
    .login-section h1 { font-size: 2rem; font-weight: 700; color: #4a4747; margin-bottom: 60px; letter-spacing: 1px; }
    .login-form { display: flex; flex-direction: column; align-items: center; gap: 25px; }
    .login-form .input-box { display: flex; align-items: center; background-color: #d9d9d9; border-radius: 50px; padding: 14px 20px; width: 480px; max-width: 90%; box-sizing: border-box; }
    .login-form .input-box i { color: #4a4a4a; font-size: 18px; margin-right: 14px; }
    .login-form .input-box input { border: none; background: none; outline: none; width: 100%; font-size: 16px; color: #333; font-family: "Poppins", sans-serif; }
    .remember-box { display: flex; align-items: center; gap: 8px; width: 480px; max-width: 90%; font-family: "Poppins", sans-serif; }
    .remember-box input[type="checkbox"] { accent-color: #3f3e3e; width: 18px; height: 18px; cursor: pointer; }
    .remember-box label { color: #3f3e3e; font-size: 15px; cursor: pointer; }
    .login-btn { background-color: #3f3e3e; color: #fff; border: none; border-radius: 50px; padding: 16px 80px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: 0.3s; }
    .login-btn:hover { background-color: #2e2d2d; }
    .forgot-text { margin-top: 25px; font-size: 15px; color: #3f3e3e; font-weight: 600; font-family: "Poppins", sans-serif; }
    .signup-text { margin-top: 20px; font-size: 15px; color: #4a4a4a; font-family: "Poppins", sans-serif; }
    .signup-text a { text-decoration: none; color: #333; font-weight: 600; }
    .message { width: 480px; max-width: 90%; padding: 15px; margin-bottom: 20px; border-radius: 10px; font-size: 16px; text-align: center; }
    .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    /* ... (CSS ส่วนอื่นๆ ของคุณ) ... */
  </style>
  <svg xmlns="http://www.w3.org/2000/svg" style="display:none;"><symbol id="instagram" viewBox="0 0 32 32"> <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" /> <rect x="10" y="10" width="12" height="12" rx="4" stroke="#bfc2c6" stroke-width="2" fill="none" /> <circle cx="16" cy="16" r="3" stroke="#bfc2c6" stroke-width="2" fill="none" /> <circle cx="21.2" cy="12.8" r="1" fill="#bfc2c6" /> </symbol><symbol id="facebook" viewBox="0 0 32 32"> <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" /> <path d="M18.5 17.5h2l.5-3h-2.5v-1.5c0-.6.2-1 .8-1H21V9.5h-2c-2 0-2.5 1.2-2.5 2.5V14.5h-1.5v3h1.5V23h3v-5.5z" fill="#bfc2c6" /> </symbol></svg>
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
  
  <section class="login-section">
  <h1>LOG IN</h1>
  <?php
    if (isset($_SESSION['message'])) {
      $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'error';
      echo '<div class="message ' . $message_type . '">' . $_SESSION['message'] . '</div>';
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
    }
  ?>
  <form class="login-form" action="login_process.php" method="POST">
    <div class="input-box"> <i class="fa-solid fa-user"></i> <input type="text" placeholder="Username" name="username" required> </div>
    <div class="input-box"> <i class="fa-solid fa-lock"></i> <input type="password" placeholder="Password" name="password" required> </div>
    <div class="remember-box"> <input type="checkbox" id="remember" name="remember"> <label for="remember">Reminder me</label> </div>
    <button type="submit" class="login-btn">LOG IN</button>
    <p class="signup-text"> Don't have an account ? <a href="signup.php">Sign up here</a> </p>
  </form>
  </section>
  
  <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> <div class="col-md-4 d-flex align-items-center"> <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Company, Inc</span> </div> <ul class="nav col-md-4 justify-content-end list-unstyled d-flex"> <li class="ms-3"> <a class="text-body-secondary" href="#" aria-label="Instagram"> <svg class="bi" width="24" height="24" aria-hidden="true"> <use xlink:href="#instagram"></use> </svg> </a> </li> <li class="ms-3"> <a class="text-body-secondary" href="#" aria-label="Facebook"><svg class="bi" width="24" height="24"> <use xlink:href="#facebook"></use> </svg> </a> </li> </ul> </footer></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      var overlay = document.getElementById('flash-overlay');
       if (overlay) { 
        overlay.classList.add('active');
        setTimeout(function() {
          overlay.classList.remove('active');
        }, 700);
      }
    });
  </script>
  <div id="flash-overlay"></div>
  
</body>
</html>