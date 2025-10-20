<?php 
session_start(); 
include 'connectdb.php'; 

// 1. รับ ID สินค้าจาก URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // ถ้าไม่มี ID หรือ ID ว่างเปล่า, อาจจะ redirect กลับหน้า Store หรือแสดงข้อผิดพลาด
    header("Location: store.php"); // สมมติว่ามีหน้า store.php
    exit();
}
$product_id = $_GET['id'];

// 2. ดึงข้อมูลสินค้าจาก Database
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // ถ้าไม่พบสินค้า
    echo "Product not found!"; // หรือแสดงหน้า 404
    exit();
}
$product = $result->fetch_assoc();
$stmt->close();

// เตรียมข้อมูลสวิตช์ (ถ้ามี)
$switches = [];
if ($product['category'] === 'Mechanical' && !empty($product['available_switches'])) {
    $switches = explode(',', $product['available_switches']); // แยก string ด้วย comma เป็น array
    $switches = array_map('trim', $switches); // ลบช่องว่างหน้า/หลังแต่ละชื่อสวิตช์
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($product['name']); ?> - KEYVERSE</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    /* (CSS ของ Navbar, Dropdown ... เหมือน index.php) */
    .navbar .container-fluid { max-width: 1600px; width: 100%; margin-left: auto; margin-right: auto; }
    .navbar { min-height: 80px; font-size: 1.25rem; }
    .nav-item { font-size: medium; padding-left: 16px; padding-right: 16px; }
    @media (max-width: 991.98px) { .nav-item { padding-left: 8px; padding-right: 8px; font-size: 0.95rem; } }
    .navbar .navbar-brand { font-size: 2rem; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .navbar .nav-link { padding-top: 1rem; padding-bottom: 1rem; }
    .navbar .nav-link.active, .navbar .nav-link:focus { color: #866953 !important; font-weight: 400; } 
    .btn-link svg { width: 30px !important; height: 30px !important; }
    body { color: #4d4c51; background-color: #f8f9fa; } /* เปลี่ยนสีพื้นหลังเล็กน้อย */
    .navbar .dropdown-toggle { color: #4d4c51; font-weight: 400; font-size: 1.1rem; }
    .navbar .dropdown-menu { font-size: 1rem; }

    /* Style สำหรับหน้า Product Detail */
    .product-image-main {
        width: 100%;
        max-width: 500px; /* จำกัดขนาดรูปใหญ่สุด */
        height: auto;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        background-color: #fff; /* พื้นหลังรูปเผื่อรูปโปร่งใส */
        padding: 10px;
        border: 1px solid #eee;
    }
    .product-title {
        font-size: 2.2rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .product-price {
        font-size: 1.8rem;
        font-weight: 500;
        color: #B19470; /* สีน้ำตาลทอง */
        margin-bottom: 20px;
    }
    .product-description {
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
    }
    .quantity-input {
        width: 80px; /* ขนาดช่องจำนวน */
    }
    .add-to-cart-btn {
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 500;
        background-color: #4d4c51;
        border-color: #4d4c51;
    }
    .add-to-cart-btn:hover {
        background-color: #3a393d;
        border-color: #3a393d;
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
            <a class="nav-link active" href="store.php">Store</a>
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
    <?php
      if (isset($_SESSION['message'])) {
        // ใช้คลาส alert ของ Bootstrap
        $message_type_class = 'alert-danger'; // สีแดง (Error) เป็นค่าเริ่มต้น
        if (isset($_SESSION['message_type'])) {
            if ($_SESSION['message_type'] === 'success') {
                $message_type_class = 'alert-success'; // สีเขียว
            } elseif ($_SESSION['message_type'] === 'warning') {
                $message_type_class = 'alert-warning'; // สีเหลือง
            } elseif ($_SESSION['message_type'] === 'info') {
                $message_type_class = 'alert-info'; // สีฟ้า
            }
        }
        
        echo '<div class="alert ' . $message_type_class . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        
        // เคลียร์ข้อความหลังจากแสดงผล (สำคัญมาก!)
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
      }
    ?>
    <div class="row g-5">
      
      <div class="col-md-6 text-center">
        <img src="<?php echo (!empty($product['image_url']) && file_exists($product['image_url'])) ? htmlspecialchars($product['image_url']) : 'img/placeholder.png'; ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image-main">
        </div>

      <div class="col-md-6">
        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="product-price">฿<?php echo number_format($product['price'], 2); ?></p>
        <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description provided.')); // nl2br ทำให้ \n เป็น <br> ?></p>
        
        <form action="cart_process.php" method="POST">
          
          <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

          <?php if ($product['category'] === 'Mechanical' && !empty($switches)): ?>
            <div class="mb-4">
              <label for="switchSelect" class="form-label">Select Switch:</label>
              <select class="form-select" id="switchSelect" name="selected_switch" required>
                <option value="" selected disabled>-- Please Choose --</option>
                <?php foreach ($switches as $switch): ?>
                  <option value="<?php echo htmlspecialchars($switch); ?>"><?php echo htmlspecialchars($switch); ?> Switch</option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
          <div class="row mb-4 align-items-end">
             <div class="col-auto">
                <label for="quantityInput" class="form-label">Quantity:</label>
                <input type="number" class="form-control quantity-input" id="quantityInput" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; // จำกัดจำนวนสูงสุดตามสต็อก ?>" required>
             </div>
             <div class="col">
                 <small class="text-muted">(<?php echo $product['stock_quantity']; ?> items available)</small>
             </div>
          </div>
          
          <button type="submit" class="btn btn-primary add-to-cart-btn" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; // Disable ถ้าของหมด ?>>
            <i class="fa-solid fa-cart-plus me-2"></i> 
            <?php echo ($product['stock_quantity'] <= 0) ? 'Out of Stock' : 'Add to Cart'; ?>
          </button>
        </form>
        </div>
    </div> </div> <div class="container"><footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top"> <div class="col-md-4 d-flex align-items-center"> <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Company, Inc</span> </div> <ul class="nav col-md-4 justify-content-end list-unstyled d-flex"> <li class="ms-3"> <a class="text-body-secondary" href="#" aria-label="Instagram"> <svg class="bi" width="24" height="24" aria-hidden="true"> <use xlink:href="#instagram"></use> </svg> </a> </li> <li class="ms-3"> <a class="text-body-secondary" href="#" aria-label="Facebook"><svg class="bi" width="24" height="24"> <use xlink:href="#facebook"></use> </svg> </a> </li> </ul> </footer></div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>