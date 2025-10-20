<?php
session_start();
include 'connectdb.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ไม่ได้ระบุ ID สินค้า";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}
$product_id = $_GET['id'];

// ดึงข้อมูล (รวม keyboard_size)
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "ไม่พบสินค้า ID: " . $product_id;
    // ... (โค้ด error เหมือนเดิม) ...
    header("Location: dashboard.php");
    exit();
}
$product = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Product - <?php echo htmlspecialchars($product['name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style> 
    body { background-color: #f8f9fa; } 
    .current-image { max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd; padding: 5px; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid" style="max-width: 1600px;">
      <a class="navbar-brand" href="dashboard.php" style="font-size: 1.5rem;">
        <i class="fa-solid fa-shield-halved"></i> KEYVERSE Admin Panel
      </a>
    </div>
  </nav>

  <div class="container" style="max-width: 1000px; margin-top: 30px;">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3>แก้ไขสินค้า ID: <?php echo $product['product_id']; ?></h3>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> กลับ</a>
      </div>
      <div class="card-body">
        
        <form action="edit_product_process.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
          <input type="hidden" name="old_image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">

          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">ชื่อสินค้า</label>
              <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ประเภทคีย์บอร์ด</label>
              <select class="form-select" name="category" required>
                <?php $categories = ['Mechanical', 'Membrane', 'Gaming', 'Ergonomic', 'Keycap', 'Switch', 'Accessory']; ?>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat; ?>" <?php if ($product['category'] == $cat) echo 'selected'; ?>>
                    <?php echo $cat; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="col-md-6 mb-3">
              <label class="form-label">ขนาดคีย์บอร์ด (ถ้ามี)</label>
              <select class="form-select" name="keyboard_size">
                <option value="" <?php if(empty($product['keyboard_size'])) echo 'selected'; ?>>-- ไม่ระบุ / N/A --</option>
                <?php $sizes = ['100%', '96%', '80%', '75%', '65%', '60%', '40%']; ?>
                <?php foreach ($sizes as $size): ?>
                   <option value="<?php echo $size; ?>" <?php if ($product['keyboard_size'] == $size) echo 'selected'; ?>>
                     <?php echo $size; ?>
                   </option>
                <?php endforeach; ?>
              </select>
            </div>
            </div>
          
          <div class="mb-3">
            <label class="form-label">รายละเอียดสินค้า</label>
            <textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ราคา</label>
              <input type="number" class="form-control" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">จำนวนสต็อก</label>
              <input type="number" class="form-control" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">สวิตช์ที่มี (ถ้ามี, คั่นด้วยลูกน้ำ)</label>
            <input type="text" class="form-control" name="available_switches" value="<?php echo htmlspecialchars($product['available_switches']); ?>" placeholder="เช่น Red,Blue,Brown">
          </div>

          <div class="mb-3">
            <label class="form-label">รูปภาพปัจจุบัน</label><br>
            <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image" class="current-image mb-2">
            <?php else: ?>
                <p class="text-muted">ไม่มีรูปภาพ</p>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label class="form-label">อัปโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
            <input type="file" class="form-control" name="product_image" accept="image/png, image/jpeg, image/webp">
          </div>
          
          <div class="text-end">
            <a href="dashboard.php" class="btn btn-secondary">ยกเลิก</a>
            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>