<?php session_start(); // **1. เริ่ม Session เพื่อเช็กสถานะล็อกอิน** ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    .navbar .container-fluid {
      max-width: 1600px;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
    }

    .navbar {
      min-height: 80px;
      font-size: 1.25rem;
    }

    .nav-item {
      font-size: medium;
      padding-left: 16px;
      padding-right: 16px;
    }

    @media (max-width: 991.98px) {
      .nav-item {
        padding-left: 8px;
        padding-right: 8px;
        font-size: 0.95rem;
      }
    }

    .navbar .navbar-brand {
      font-size: 2rem;
      padding-top: 0.5rem;
      padding-bottom: 0.5rem;
    }

    .navbar .nav-link {
      padding-top: 1rem;
      padding-bottom: 1rem;
    }

    .navbar .nav-link.active,
    .navbar .nav-link:focus {
      color: #000 !important;
      font-weight: bold;
    }

    .btn-link svg {
      width: 30px !important;
      height: 30px !important;
    }

    body {
      color: #4d4c51;
      background-color: #f8f9fa;
    }

    /* Flash overlay styles */
    #flash-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: white;
      opacity: 0;
      pointer-events: none;
      z-index: 9999;
      transition: opacity 0.25s;
    }
    #flash-overlay.active {
      opacity: 1;
      pointer-events: auto;
      animation: flashAnim 0.7s linear;
    }
    @keyframes flashAnim {
      0% { opacity: 1; }
      80% { opacity: 1; }
      100% { opacity: 0; }
    }
    
    /* Style สำหรับ Dropdown ที่เราเพิ่มเข้ามา */
    .navbar .dropdown-toggle {
        color: #4d4c51;
        font-weight: 400;
        font-size: 1.1rem; /* ขนาดตัวอักษรของชื่อ */
    }
    .navbar .dropdown-menu {
        font-size: 1rem;
    }
  </style>
  <svg xmlns="http://www.w3.org/2000/svg" style="display:none;">
    <symbol id="instagram" viewBox="0 0 32 32">
      <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" />
      <rect x="10" y="10" width="12" height="12" rx="4" stroke="#bfc2c6" stroke-width="2" fill="none" />
      <circle cx="16" cy="16" r="3" stroke="#bfc2c6" stroke-width="2" fill="none" />
      <circle cx="21.2" cy="12.8" r="1" fill="#bfc2c6" />
    </symbol>
    <symbol id="facebook" viewBox="0 0 32 32">
      <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" />
      <path d="M18.5 17.5h2l.5-3h-2.5v-1.5c0-.6.2-1 .8-1H21V9.5h-2c-2 0-2.5 1.2-2.5 2.5V14.5h-1.5v3h1.5V23h3v-5.5z"
        fill="#bfc2c6" />
    </symbol>
  </svg>
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
            <a class="nav-link active" href="switches.php">Switches</a>
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
          <a href="search.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" width="28" height="28">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
          </a>
          <?php
          // คำนวณจำนวนสินค้าในตะกร้า (นับรวมทุกชิ้น ไม่ใช่แค่ประเภท)
          $cart_count = 0;
          if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
              foreach ($_SESSION['cart'] as $item) {
                  $cart_count += $item['quantity']; // นับตามจำนวน (quantity)
              }
          }
          ?>
          <a href="cart.php" id="cart-icon-nav" class="btn btn-link p-2 position-relative" style="box-shadow: none" aria-label="Cart">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="28" height="28">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.125A4.125 4.125 0 008.25 7.125V10.5M3.375 9.75l1.125 9A2.25 2.25 0 006.75 21h10.5a2.25 2.25 0 002.25-2.25l1.125-9a.75.75 0 00-.75-.75H4.125a.75.75 0 00-.75.75z" />
            </svg>
            <?php if ($cart_count > 0): // ถ้ามีสินค้าในตะกร้า ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $cart_count; ?>
                <span class="visually-hidden">items in cart</span>
              </span>
            <?php endif; ?>
          </a>

          <?php if (isset($_SESSION['user_id'])): // --- ถ้าล็อกอินแล้ว --- ?>
            
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); // แสดงชื่อ User ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <?php if ($_SESSION['role'] === 'admin'): // ถ้าเป็นแอดมิน ให้มีลิงก์ไป Dashboard ?>
                  <li><a class="dropdown-item" href="dashboard.php">Admin Dashboard</a></li>
                  <li><hr class="dropdown-divider"></li>
                <?php endif; ?>
                <li><a class="dropdown-item" href="profile.php">My Account</a></li>
                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
              </ul>
            </div>

          <?php else: // --- ถ้ายังไม่ล็อกอิน --- ?>

            <a href="login.php" class="btn btn-link p-2" style="box-shadow: none" aria-label="Login/Register">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                stroke="currentColor" width="28" height="28">
                <path stroke-linecap="round" stroke-linejoin="round" 
                  d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A1.875 1.875 0 0118.375 22.5H5.625a1.875 1.875 0 01-1.124-2.382z" />
              </svg>
            </a>

          <?php endif; // --- จบการเช็ก --- ?>

        </div>
      </div>
    </div>
  </nav>
  <div class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top "></div>   



  
  

  <div class="container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
      <div class="col-md-4 d-flex align-items-center">
        <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Company, Inc</span>
      </div>
      <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
        <li class="ms-3">
          <a class="text-body-secondary" href="#" aria-label="Instagram">
            <svg class="bi" width="24" height="24" aria-hidden="true">
              <use xlink:href="#instagram"></use>
            </svg>
          </a>
        </li>
        <li class="ms-3">
          <a class="text-body-secondary" href="#" aria-label="Facebook"><svg class="bi" width="24" height="24">
              <use xlink:href="#facebook"></use>
            </svg>
          </a>
        </li>
      </ul>
    </footer>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
  <script>
    function scrollProduct(dir) {
      const el = document.getElementById("product-scroll");
      const cardWidth = el.querySelector(".card").offsetWidth + 16; // card + margin
      el.scrollBy({ left: dir * cardWidth * 2, behavior: "smooth" });
    }
    // Show flash overlay after page load
    window.addEventListener('DOMContentLoaded', function() {
      var overlay = document.getElementById('flash-overlay');
      overlay.classList.add('active');
      setTimeout(function() {
        overlay.classList.remove('active');
      }, 700); // duration matches animation
    });
  </script>
  <div id="flash-overlay"></div>
  
  </body>

</html>