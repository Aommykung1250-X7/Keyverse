<?php 
session_start(); 
include 'connectdb.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KEYVERSE - Mechanical Keyboards</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    /* (CSS ของ Navbar, Dropdown ... เหมือนเดิม) */
    .slide-list {
      /* สำหรับ Firefox */
      scrollbar-width: none; 
      /* สำหรับ IE/Edge (เก่า) */
      -ms-overflow-style: none;  
    }
    /* สำหรับ Chrome, Safari, Opera (Webkit) */
    .slide-list::-webkit-scrollbar {
      display: none; 
    }
    .carousel-item { min-height: 400px; }
    .navbar .container-fluid { max-width: 1600px; width: 100%; margin-left: auto; margin-right: auto; }
    .navbar { min-height: 80px; font-size: 1.25rem; }
    .nav-item { font-size: medium; padding-left: 16px; padding-right: 16px; }
    @media (max-width: 991.98px) { .nav-item { padding-left: 8px; padding-right: 8px; font-size: 0.95rem; } }
    .navbar .navbar-brand { font-size: 2rem; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .navbar .nav-link { padding-top: 1rem; padding-bottom: 1rem; }
    .navbar .nav-link.active, .navbar .nav-link:focus { color: #000 !important; font-weight: bold; } 
    .btn-link svg { width: 30px !important; height: 30px !important; }
    body { color: #4d4c51; background-color: #f8f9fa;}
    #flash-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: white; opacity: 0; pointer-events: none; z-index: 9999; transition: opacity 0.25s; }
    #flash-overlay.active { opacity: 1; pointer-events: auto; animation: flashAnim 0.7s linear; }
    @keyframes flashAnim { 0% { opacity: 1; } 80% { opacity: 1; } 100% { opacity: 0; } }
    .navbar .dropdown-toggle { color: #4d4c51; font-weight: 400; font-size: 1.1rem; }
    .navbar .dropdown-menu { font-size: 1rem; }

    /* (CSS ของ Keyboard Page - เหมือนเดิม) */
    .button-container {
      display: flex;
      gap: 220px;
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: 40px;
    }
    .filter-btn { border: 2px solid #555; background-color: transparent; color: #333; font-weight: bold; font-size: 16px; padding: 10px 30px; border-radius: 50px; cursor: pointer; text-decoration: none; transition: all 0.2s ease; min-width: 150px; text-align: center; }
    .filter-btn:hover { background-color: #f0f0f0; }
    .filter-btn.active { background-color: #ccc; border-color: #555; }
    .slide-section { width: 100%; padding: 15px 45px 30px 45px; background-color: #fff; border-bottom: 1px solid #eee; margin-bottom: 20px;}
    .slide-section h4 { font-weight: bold; color: #000; margin-bottom: 8px; }
    .slide-section p.section-desc { color: #555; margin-bottom: 20px;}
    .slide-container { position: relative; width: 100%; overflow: hidden; }
    .slide-list { display: flex; gap: 32px; overflow-x: auto; scroll-behavior: smooth; padding-bottom: 15px; }
    .product-card { flex: 0 0 300px; background: #fff; border-radius: 18px; box-shadow: 0 2px 8px #0000001a; overflow: hidden; display: flex; flex-direction: column; margin-bottom: 8px; border: 1px solid #eee; text-decoration: none; color: inherit;} /* Add text-decoration & color */
    .product-card:hover { box-shadow: 0 4px 12px #00000026;} /* Add hover effect */
    .product-card img { width: 100%; height: 170px; object-fit: cover; }
    .product-card-body { padding: 15px; text-align: left; flex-grow: 1;} /* Add flex-grow */
    .product-card-title { font-size: 1.05rem; font-weight: bold; margin-bottom: 5px; color: #222; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .product-card-detail { font-size: 0.95rem; color: #666; margin-bottom: 8px; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .product-card-price { font-weight: bold; color: #444; font-size: 1rem; margin-top: auto;} /* Add margin-top auto */
    .slide-btn { position: absolute; top: calc(50% - 15px); transform: translateY(-50%); z-index: 2; background: #fffa; border: none; box-shadow: 0 1px 5px #0002; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0.85; transition: opacity 0.2s; }
    .slide-btn:hover { opacity: 1; }
    .slide-btn.left { left: 5px; }
    .slide-btn.right { right: 5px; }
    .slide-btn svg { width: 24px; height: 24px; color: #555; }
    @media (max-width: 900px) { .product-card { flex: 0 0 75vw; } .slide-list { gap: 16px; } }
    .no-products { text-align: center; color: #888; padding: 20px; width: 100%;} /* Ensure full width */
  </style>
  
  <svg xmlns="http://www.w3.org/2000/svg" style="display:none;"><symbol id="instagram" viewBox="0 0 32 32"><circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" /><rect x="10" y="10" width="12" height="12" rx="4" stroke="#bfc2c6" stroke-width="2" fill="none" /><circle cx="16" cy="16" r="3" stroke="#bfc2c6" stroke-width="2" fill="none" /><circle cx="21.2" cy="12.8" r="1" fill="#bfc2c6" /></symbol><symbol id="facebook" viewBox="0 0 32 32"><circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" /><path d="M18.5 17.5h2l.5-3h-2.5v-1.5c0-.6.2-1 .8-1H21V9.5h-2c-2 0-2.5 1.2-2.5 2.5V14.5h-1.5v3h1.5V23h3v-5.5z" fill="#bfc2c6" /></symbol></svg>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary rounded" aria-label="Thirteenth navbar example">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample11"
        aria-controls="navbarsExample11" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11">
        <a class="navbar-brand col-lg-3 me-0" style="padding-left: 18px; color: #4d4c51" href="index.php">KEYVERSE</a>
        <ul class="navbar-nav col-lg-6 justify-content-lg-center">
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="store.php">Store</a> </li>
          <li class="nav-item">
            <a class="nav-link active" href="keyboard.php">Keyboards</a> </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="switches.php">Switches</a> </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="keycap.php">Keycaps</a> </li>
          <li class="nav-item">
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="accessories.php">Accessories</a> </li>
         
        </ul>
        
        <div class="d-lg-flex col-lg-3 justify-content-lg-end align-items-center">
          
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
  <div class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top "></div>

  <div class="section" style="width: 100%; padding: 15px 45px 15px 45px;">
    <h2>Mechanical Keyboards</h2> <p class="subtitle">
        สัมผัสความแตกต่างของการพิมพ์ด้วย Mechanical Keyboard ที่ตอบสนองฉับไวและทนทาน<br />
        เลือกสวิตช์และขนาดที่เหมาะกับสไตล์ของคุณ
      </p>
  </div>
  <div class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top "></div>

  <div class="button-container">
    <a href="keyboard.php" class="filter-btn active">Mechanical</a>
    <a href="membrane.php" class="filter-btn">Membrane</a>
    <a href="gaming.php" class="filter-btn ">Gaming</a>
    <a href="ergonomic.php" class="filter-btn">Ergonomic</a>
  </div>

  <div class="slide-section">
    <h4>Full-size (100%) Mechanical</h4>
    <p class="section-desc">Layout ครบครัน ตอบสนองแม่นยำ เหมาะสำหรับทุกการใช้งาน</p>
    <div class="slide-container">
      <button class="slide-btn left" onclick="slideScroll('slide-list-100', -1)">
        <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" fill="currentColor"/></svg>
      </button>
      <div class="slide-list" id="slide-list-100"> 
        <?php
          // ดึงข้อมูล Mechanical 100%
          $sql_100 = "SELECT * FROM products WHERE category = 'Mechanical' AND keyboard_size = '100%' ORDER BY product_id ASC";
          $result_100 = $conn->query($sql_100);

          if ($result_100->num_rows > 0) {
            while ($row = $result_100->fetch_assoc()) {
        ?>
              <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="product-card"> 
                <img src="<?php echo (!empty($row['image_url']) && file_exists($row['image_url'])) ? htmlspecialchars($row['image_url']) : 'img/placeholder.png'; ?>" 
                     alt="<?php echo htmlspecialchars($row['name']); ?>">
                <div class="product-card-body">
                  <div class="product-card-title"><?php echo htmlspecialchars($row['name']); ?></div>
                  <div class="product-card-detail"><?php echo htmlspecialchars(substr($row['description'] ?? '', 0, 80)) . '...'; // แสดง Description ย่อ ?></div>
                  <div class="product-card-price">START <?php echo number_format($row['price'], 0); ?> BAHT</div>
                </div>
              </a>
        <?php 
            } 
          } else {
            echo "<p class='no-products'>No 100% Mechanical keyboards found.</p>";
          } 
        ?>
      </div>
      <button class="slide-btn right" onclick="slideScroll('slide-list-100', 1)">
        <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" fill="currentColor"/></svg>
      </button>
    </div>
  </div>

  <div class="slide-section">
    <h4>75% Mechanical</h4>
    <p class="section-desc">ขนาดกระทัดรัด แต่ยังคงปุ่มฟังก์ชันที่จำเป็นครบ</p>
    <div class="slide-container">
      <button class="slide-btn left" onclick="slideScroll('slide-list-75', -1)">
         <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" fill="currentColor"/></svg>
      </button>
      <div class="slide-list" id="slide-list-75">
        <?php
          $sql_75 = "SELECT * FROM products WHERE category = 'Mechanical' AND keyboard_size = '75%' ORDER BY product_id ASC";
          $result_75 = $conn->query($sql_75);

          if ($result_75->num_rows > 0) {
            while ($row = $result_75->fetch_assoc()) {
        ?>
              <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="product-card">
                <img src="<?php echo (!empty($row['image_url']) && file_exists($row['image_url'])) ? htmlspecialchars($row['image_url']) : 'img/placeholder.png'; ?>" 
                     alt="<?php echo htmlspecialchars($row['name']); ?>">
                <div class="product-card-body">
                  <div class="product-card-title"><?php echo htmlspecialchars($row['name']); ?></div>
                  <div class="product-card-detail"><?php echo htmlspecialchars(substr($row['description'] ?? '', 0, 80)) . '...'; ?></div>
                  <div class="product-card-price">START <?php echo number_format($row['price'], 0); ?> BAHT</div>
                </div>
              </a>
        <?php 
            } 
          } else {
            echo "<p class='no-products'>No 75% Mechanical keyboards found.</p>";
          } 
        ?>
      </div>
      <button class="slide-btn right" onclick="slideScroll('slide-list-75', 1)">
         <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" fill="currentColor"/></svg>
      </button>
    </div>
  </div>

   <div class="slide-section">
    <h4>60% Mechanical</h4>
    <p class="section-desc">พกพาสะดวก ดีไซน์มินิมอล ประหยัดพื้นที่บนโต๊ะทำงาน</p>
    <div class="slide-container">
      <button class="slide-btn left" onclick="slideScroll('slide-list-60', -1)">
         <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" fill="currentColor"/></svg>
      </button>
      <div class="slide-list" id="slide-list-60">
        <?php
          $sql_60 = "SELECT * FROM products WHERE category = 'Mechanical' AND keyboard_size = '60%' ORDER BY product_id ASC";
          $result_60 = $conn->query($sql_60);

          if ($result_60->num_rows > 0) {
            while ($row = $result_60->fetch_assoc()) {
        ?>
              <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="product-card">
                <img src="<?php echo (!empty($row['image_url']) && file_exists($row['image_url'])) ? htmlspecialchars($row['image_url']) : 'img/placeholder.png'; ?>" 
                     alt="<?php echo htmlspecialchars($row['name']); ?>">
                <div class="product-card-body">
                  <div class="product-card-title"><?php echo htmlspecialchars($row['name']); ?></div>
                  <div class="product-card-detail"><?php echo htmlspecialchars(substr($row['description'] ?? '', 0, 80)) . '...'; ?></div>
                  <div class="product-card-price">START <?php echo number_format($row['price'], 0); ?> BAHT</div>
                </div>
              </a>
        <?php 
            } 
          } else {
            echo "<p class='no-products'>No 60% Mechanical keyboards found.</p>";
          } 
          
          $conn->close(); 
        ?>
      </div>
      <button class="slide-btn right" onclick="slideScroll('slide-list-60', 1)">
         <svg viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z" fill="currentColor"/></svg>
      </button>
    </div>
  </div>

  <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> <div class="col-md-4 d-flex align-items-center"> <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Company, Inc</span> </div> <ul class="nav col-md-4 justify-content-end list-unstyled d-flex"> <li class="ms-3"> <a class="text-body-secondary" href="#" aria-label="Instagram"> <svg class="bi" width="24" height="24" aria-hidden="true"> <use xlink:href="#instagram"></use> </svg> </a> </li> <li class="ms-3"> <a class="text-body-secondary" href="#" aria-label="Facebook"><svg class="bi" width="24" height="24"> <use xlink:href="#facebook"></use> </svg> </a> </li> </ul> </footer></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  
  <script>
    // (Flash overlay script ... เหมือนเดิม)
    window.addEventListener('DOMContentLoaded', function() {
      var overlay = document.getElementById('flash-overlay');
      if (overlay) { 
        overlay.classList.add('active');
        setTimeout(function() {
          overlay.classList.remove('active');
        }, 700); 
      }
    });

    // (Slide Scroll script ... เหมือนเดิม)
    function slideScroll(elementId, direction) {
      const el = document.getElementById(elementId);
      if (!el) return; 
      const card = el.querySelector(".product-card"); 
      if (!card) return; 
      const computedStyle = window.getComputedStyle(el);
      const gap = parseFloat(computedStyle.gap) || 32; 
      const cardWidth = card.offsetWidth + gap; 
      const scrollAmount = direction * cardWidth * 2; 
      el.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  </script>
  <div id="flash-overlay"></div>
  
</body>
</html>