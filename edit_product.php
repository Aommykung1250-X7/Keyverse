<?php
session_start();
include 'connectdb.php';

// (โค้ด PHP ส่วนบน ... ตรวจสอบสิทธิ์, ดึงข้อมูล $product เหมือนเดิม)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { /*...*/ }
if (!isset($_GET['id']) || empty($_GET['id'])) { /*...*/ }
$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { /*...*/ }
$product = $result->fetch_assoc(); // $product มี sub_category และ keyboard_size
$stmt->close();
// $conn->close();

// กำหนดค่า Sub-Category ที่เป็นไปได้ทั้งหมด (เหมือนหน้า Add)
$possible_sub_categories = [
    'Keycap' => ['Brand', 'Handmade'],
    'Switch' => ['Linear', 'Tactile', 'Clicky', 'Low Profile'],
    'Accessory' => ['Wrist Rest', 'Deskmat', 'Cable', 'Cleaning Kit', 'Switch Puller']
];
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
    .current-image { /* ... */ }
    /* ซ่อนช่องที่ไม่จำเป็นไว้ก่อน */
    #subCategoryDiv, #keyboardSizeDiv { display: none; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark"> </nav>

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
              <label class="form-label">ประเภท</label>
              <select class="form-select" id="categorySelect" name="category" required onchange="updateDependentFields()">
                <?php $categories = ['Mechanical', 'Membrane', 'Gaming', 'Ergonomic', 'Keycap', 'Switch', 'Accessory']; ?>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat; ?>" <?php if ($product['category'] == $cat) echo 'selected'; ?>>
                    <?php echo $cat; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 mb-3" id="subCategoryDiv">
               <label class="form-label" id="subCategoryLabel">ประเภทย่อย</label>
               <select class="form-select" id="subCategorySelect" name="sub_category">
                  <option value="">-- เลือกประเภทย่อย --</option>
               </select>
            </div>

            <div class="col-md-6 mb-3" id="keyboardSizeDiv">
              <label class="form-label">ขนาดคีย์บอร์ด</label>
              <select class="form-select" id="keyboardSizeSelect" name="keyboard_size">
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
            <label class="form-label">สวิตช์ที่มี (สำหรับ Mechanical เท่านั้น)</label>
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

  <?php if(isset($conn)) {$conn->close();} ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <script>
    const allSubCategories = <?php echo json_encode($possible_sub_categories); ?>;
    const keyboardCategories = ['Mechanical', 'Membrane', 'Gaming', 'Ergonomic'];
    // เก็บค่า Sub-Category และ Keyboard Size เดิมจาก PHP
    const currentSubCategory = <?php echo json_encode($product['sub_category']); ?>;
    const currentKeyboardSize = <?php echo json_encode($product['keyboard_size']); ?>; // ดึงค่า keyboard_size เดิม

    function updateDependentFields(isInitialLoad = false) {
      const categorySelect = document.getElementById('categorySelect');
      const subCategoryDiv = document.getElementById('subCategoryDiv');
      const subCategorySelect = document.getElementById('subCategorySelect');
      const subCategoryLabel = document.getElementById('subCategoryLabel');
      const keyboardSizeDiv = document.getElementById('keyboardSizeDiv');
      const keyboardSizeSelect = document.getElementById('keyboardSizeSelect');
      const selectedCategory = categorySelect.value;

      // --- 1. จัดการช่อง Sub-Category ---
      subCategorySelect.innerHTML = '';
      if (allSubCategories[selectedCategory]) {
          if (selectedCategory === 'Keycap') {
              subCategoryLabel.textContent = 'รูปแบบ Keycap';
              subCategorySelect.innerHTML = '<option value="">-- เลือกรูปแบบ --</option>';
          } else {
              subCategoryLabel.textContent = 'ประเภทย่อย';
              subCategorySelect.innerHTML = '<option value="">-- เลือกประเภทย่อย --</option>';
          }

          allSubCategories[selectedCategory].forEach(subCat => {
              const option = document.createElement('option');
              option.value = subCat;
              option.textContent = subCat;
              // ถ้าเป็นตอนโหลดหน้า หรือ ค่า subCat ตรงกับค่าเดิม ให้เลือก option นี้
              if (isInitialLoad && subCat === currentSubCategory) {
                  option.selected = true;
              }
              subCategorySelect.appendChild(option);
          });
          subCategoryDiv.style.display = 'block';
          subCategorySelect.required = true;
      } else {
          subCategoryDiv.style.display = 'none';
          subCategorySelect.required = false;
          if (!isInitialLoad) subCategorySelect.value = ""; // เคลียร์ค่าเฉพาะเมื่อ User เปลี่ยน ไม่ใช่ตอนโหลด
      }

      // --- 2. จัดการช่อง Keyboard Size ---
      if (keyboardCategories.includes(selectedCategory)) {
          keyboardSizeDiv.style.display = 'block';
          // ไม่ต้องเลือกค่าเดิม เพราะ PHP จัดการให้แล้วตอนสร้าง <option>
      } else {
          keyboardSizeDiv.style.display = 'none';
          // keyboardSizeSelect.required = false;
          if (!isInitialLoad) keyboardSizeSelect.value = ""; // เคลียร์ค่าเฉพาะเมื่อ User เปลี่ยน ไม่ใช่ตอนโหลด
      }
    }

    // *** เรียกใช้ครั้งแรกเมื่อโหลดหน้าเว็บ ***
    document.addEventListener('DOMContentLoaded', function() {
        updateDependentFields(true); // ส่ง true ไปบอกว่าเป็น Initial Load
    });
  </script>
   </body>
</html>