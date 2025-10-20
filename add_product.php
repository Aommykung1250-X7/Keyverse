<?php
session_start();
include 'connectdb.php'; 

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
  <style> body { background-color: #f8f9fa; } </style>
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
              <label class="form-label">ประเภทคีย์บอร์ด</label>
              <select class="form-select" name="category" required>
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
            
            <div class="col-md-6 mb-3">
              <label class="form-label">ขนาดคีย์บอร์ด (ถ้ามี)</label>
              <select class="form-select" name="keyboard_size">
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
            <label class="form-label">สวิตช์ที่มี (ถ้ามี, คั่นด้วยลูกน้ำ)</label>
            <input type="text" class="form-control" name="available_switches" placeholder="เช่น Red,Blue,Brown (เว้นว่างถ้าไม่มี)">
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
</body>
</html>