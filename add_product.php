<?php
session_start();
include 'connectdb.php';

// กำหนดค่า Sub-Category ที่เป็นไปได้ทั้งหมด
$possible_sub_categories = [
    'Keycap' => ['Brand', 'Handmade'], // เพิ่ม Keycap
    'Switch' => ['Linear', 'Tactile', 'Clicky', 'Low Profile'],
    'Accessory' => ['Wrist Rest', 'Deskmat', 'Cable', 'Cleaning Kit', 'Switch Puller']
];

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add New Product - KEYVERSE Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
      body { background-color: #f8f9fa; }
      /* ซ่อนช่องที่ไม่จำเป็นไว้ก่อน */
      #subCategoryDiv, #keyboardSizeDiv { display: none; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid" style="max-width: 1600px;"> <a class="navbar-brand" href="dashboard.php" style="font-size: 1.5rem;"><i class="fa-solid fa-shield-halved"></i> KEYVERSE Admin Panel</a> </div>
  </nav>

  <div class="container" style="max-width: 1000px; margin-top: 30px;">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3>เพิ่มสินค้าใหม่</h3>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> กลับ</a>
      </div>
      <div class="card-body">

        <form action="add_product_process.php" method="POST" enctype="multipart/form-data">

          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">ชื่อสินค้า</label>
              <input type="text" class="form-control" name="name" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ประเภท</label>
              <select class="form-select" id="categorySelect" name="category" required onchange="updateDependentFields()">
                <option value="" selected>-- เลือกประเภท --</option>
                <option value="Mechanical">Mechanical</option>
                <option value="Membrane">Membrane</option>
                <option value="Gaming">Gaming</option>
                <option value="Ergonomic">Ergonomic</option>
                <option value="Keycap">Keycap</option>
                <option value="Switch">Switch</option>
                <option value="Accessory">Accessory</option>
              </select>
            </div>

            <div class="col-md-6 mb-3" id="subCategoryDiv">
              <label class="form-label" id="subCategoryLabel">ประเภทย่อย</label> <select class="form-select" id="subCategorySelect" name="sub_category">
                 <option value="">-- เลือกประเภทย่อย --</option>
              </select>
            </div>

            <div class="col-md-6 mb-3" id="keyboardSizeDiv">
              <label class="form-label">ขนาดคีย์บอร์ด</label>
              <select class="form-select" id="keyboardSizeSelect" name="keyboard_size">
                <option value="" selected>-- ไม่ระบุ / N/A --</option>
                <option value="100%">100% (Full Size)</option>
                <option value="96%">96%</option>
                <option value="80%">80% (TKL)</option>
                <option value="75%">75%</option>
                <option value="65%">65%</option>
                <option value="60%">60%</option>
                <option value="40%">40%</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">รายละเอียดสินค้า</label>
            <textarea class="form-control" name="description" rows="5"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ราคา</label>
              <input type="number" class="form-control" name="price" step="0.01" value="0.00" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">จำนวนสต็อก</label>
              <input type="number" class="form-control" name="stock_quantity" value="0" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">สวิตช์ที่มี (สำหรับ Mechanical เท่านั้น)</label>
            <input type="text" class="form-control" name="available_switches" placeholder="เช่น Red,Blue,Brown">
          </div>

          <div class="mb-3">
            <label class="form-label">อัปโหลดรูปภาพ</label>
            <input type="file" class="form-control" name="product_image" accept="image/png, image/jpeg, image/webp">
          </div>

          <div class="text-end">
            <a href="dashboard.php" class="btn btn-secondary">ยกเลิก</a>
            <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <script>
    const allSubCategories = <?php echo json_encode($possible_sub_categories); ?>;
    const keyboardCategories = ['Mechanical', 'Membrane', 'Gaming', 'Ergonomic']; // รายชื่อ Category ที่เป็น Keyboard

    function updateDependentFields() {
      const categorySelect = document.getElementById('categorySelect');
      const subCategoryDiv = document.getElementById('subCategoryDiv');
      const subCategorySelect = document.getElementById('subCategorySelect');
      const subCategoryLabel = document.getElementById('subCategoryLabel'); // Label ของ Sub-Category
      const keyboardSizeDiv = document.getElementById('keyboardSizeDiv');
      const keyboardSizeSelect = document.getElementById('keyboardSizeSelect'); // Dropdown Keyboard Size
      const selectedCategory = categorySelect.value;

      // --- 1. จัดการช่อง Sub-Category ---
      subCategorySelect.innerHTML = ''; // ล้างตัวเลือกเก่าเสมอ
      if (allSubCategories[selectedCategory]) {
          // กำหนด Label ให้ถูกต้อง
          if (selectedCategory === 'Keycap') {
              subCategoryLabel.textContent = 'รูปแบบ Keycap';
              subCategorySelect.innerHTML = '<option value="">-- เลือกรูปแบบ --</option>'; // ข้อความเริ่มต้น
          } else {
              subCategoryLabel.textContent = 'ประเภทย่อย';
              subCategorySelect.innerHTML = '<option value="">-- เลือกประเภทย่อย --</option>';
          }

          // สร้างตัวเลือกใหม่
          allSubCategories[selectedCategory].forEach(subCat => {
              const option = document.createElement('option');
              option.value = subCat;
              option.textContent = subCat;
              subCategorySelect.appendChild(option);
          });
          subCategoryDiv.style.display = 'block'; // แสดงช่อง
          subCategorySelect.required = true; // บังคับเลือก
      } else {
          subCategoryDiv.style.display = 'none'; // ซ่อนช่อง
          subCategorySelect.required = false; // ไม่บังคับ
          subCategorySelect.value = ""; // เคลียร์ค่า
      }

      // --- 2. จัดการช่อง Keyboard Size ---
      if (keyboardCategories.includes(selectedCategory)) {
          keyboardSizeDiv.style.display = 'block'; // แสดงช่อง
          // keyboardSizeSelect.required = true; // อาจจะไม่ต้องบังคับเลือกขนาดก็ได้
      } else {
          keyboardSizeDiv.style.display = 'none'; // ซ่อนช่อง
          // keyboardSizeSelect.required = false;
          keyboardSizeSelect.value = ""; // เคลียร์ค่า
      }
    }

    // เรียกครั้งแรกเผื่อมี default หรือ error validation (แต่ Add Product ไม่น่ามี)
    // document.addEventListener('DOMContentLoaded', updateDependentFields);
  </script>
   </body>
</html>