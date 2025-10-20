<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KEYVERSE</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <style>
    .carousel-item {
      min-height: 400px;
    }

    .navbar {
      min-height: 80px;
      font-size: 1.25rem;
    }

    .nav-item {
      font-size: medium ;
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

    .type-content {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 48px;
      margin: 40px 0 40px 0;
      flex-wrap: wrap;
    }
    .type-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 180px;
      text-align: center;
      text-decoration: none;
      color: #333;
      transition: transform 0.15s;
    }
    .type-card:hover {
      transform: translateY(-6px) scale(1.04);
      color: #866953;
    }
    .type-card img {
      width: 200px;
      height: 110px;
      object-fit: contain;
      margin-bottom: 16px;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
    }
    .type-card-title {
      font-size: 1.15rem;
      font-weight: 500;
      margin-top: 8px;
      margin-bottom: 0;
    }
    @media (max-width: 900px) {
      .type-content {
        gap: 24px;
      }
      .type-card {
        width: 44vw;
        min-width: 140px;
      }
    }

    .slide-sell-list {
      width: 100%;
      padding: 15px 45px 15px 45px;
      background-color: #fff;
    }
    .sell-slide-container {
      position: relative;
      width: 100%;
      overflow: hidden;
      margin-top: 16px;
    }
    .sell-slide-list {
      display: flex;
      gap: 32px;
      overflow-x: auto;
      scroll-behavior: smooth;
      padding-bottom: 8px;
    }
    .sell-card {
      flex: 0 0 320px;
      background: linear-gradient(to bottom, #fff 80%, #f3f4f6 100%);
      border-radius: 24px;
      box-shadow: 0 2px 12px #0001;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 8px;
    }
    .sell-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 24px 24px 0 0;
    }
    .sell-card-body {
      width: 100%;
      padding: 18px 18px 12px 18px;
      background: #f3f4f6;
      border-radius: 0 0 24px 24px;
      text-align: left;
    }
    .sell-card-title {
      font-size: 1.1rem;
      font-weight: bold;
      margin-bottom: 6px;
      color: #222;
    }
    .sell-card-detail {
      font-size: 0.98rem;
      color: #666;
      margin-bottom: 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .sell-card-price {
      font-weight: bold;
      color: #444;
      font-size: 1.05rem;
      margin-top: 4px;
    }
    .sell-slide-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 2;
      background: #fff;
      border: none;
      box-shadow: 0 2px 8px #0002;
      border-radius: 50%;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0.92;
    }
    .sell-slide-btn.left { left: 0; }
    .sell-slide-btn.right { right: 0; }
    .sell-slide-btn svg { width: 28px; height: 28px; }
    @media (max-width: 900px) {
      .sell-card { flex: 0 0 80vw; }
      .sell-slide-list { gap: 16px; }
    }

    .accessories-section {
      width: 100%;
      padding: 15px 45px 15px 45px;
      background-color: #fff;
    }
    .accessories-slide-container {
      position: relative;
      width: 100%;
      overflow: hidden;
      margin-top: 16px;
    }
    .accessories-slide-list {
      display: flex;
      gap: 32px;
      overflow-x: auto;
      scroll-behavior: smooth;
      padding-bottom: 8px;
    }
    .accessories-card {
      flex: 0 0 320px;
      background: linear-gradient(to bottom, #fff 80%, #f3f4f6 100%);
      border-radius: 24px;
      box-shadow: 0 2px 12px #0001;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 8px;
    }
    .accessories-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 24px 24px 0 0;
    }
    .accessories-card-body {
      width: 100%;
      padding: 18px 18px 12px 18px;
      background: #f3f4f6;
      border-radius: 0 0 24px 24px;
      text-align: left;
    }
    .accessories-card-title {
      font-size: 1.1rem;
      font-weight: bold;
      margin-bottom: 6px;
      color: #222;
    }
    .accessories-card-detail {
      font-size: 0.98rem;
      color: #666;
      margin-bottom: 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .accessories-card-price {
      font-weight: bold;
      color: #444;
      font-size: 1.05rem;
      margin-top: 4px;
    }
    .accessories-slide-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 2;
      background: #fff;
      border: none;
      box-shadow: 0 2px 8px #0002;
      border-radius: 50%;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0.92;
    }
    .accessories-slide-btn.left { left: 0; }
    .accessories-slide-btn.right { right: 0; }
    .accessories-slide-btn svg { width: 28px; height: 28px; }
    @media (max-width: 900px) {
      .accessories-card { flex: 0 0 80vw; }
      .accessories-slide-list { gap: 16px; }
    }
   .button-container {
      display: flex;
      gap: 220px;
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: 40px;
    }

    .filter-btn {
      border: 2px solid #555;
      background-color: transparent;
      color: #333;
      font-weight: bold;
      font-size: 16px;
      padding: 12px 35px;
      border-radius: 50px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
      min-width: 160px;
      text-align: center;
    }

    .filter-btn:hover {
      background-color: #f0f0f0;
    }

    .filter-btn.active {
      background-color: #ccc;
      border-color: #555;
    }
    /* ส่วนหัว */
.keyboard-section {
  text-align: left;
  padding: 40px 100px;
}

.keyboard-section h2 {
  font-size: 1.5rem;
  color: #111;
  font-weight: 700;
}

.keyboard-section h2 span {
  color: #888;
  font-weight: 400;
}

/* กล่องรวมสินค้า */
.product-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center; /* ✅ ให้อยู่กลางแนวนอน */
  gap: 50px;               /* ✅ ระยะห่างระหว่างสินค้า */
  max-width: 1200px;       /* ✅ จำกัดความกว้าง */
  margin: 40px auto;       /* ✅ จัดให้อยู่กลางหน้า */
}

/* การ์ดสินค้า */
.product-card {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  width: 260px;
  flex-direction: column;
  justify-content: space-between;
  height: 360px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

/* รูปสินค้า */
.product-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

/* ชื่อสินค้า */
.product-card h3 {
  font-size: 1rem;
  margin: 10px 15px 0;
  color: #222;
  min-height: 40px;
}

/* รายละเอียด */
.product-card p {
  font-size: 0.85rem;
  color: #777;
  margin: 5px 15px;
  min-height: 35px;
}

/* ราคา */
.product-card .price {
  display: block;
  font-weight: 600;
  color: #333;
  margin: 10px 15px 15px;
}
  </style>
  <!-- SVG ICONS FOR FOOTER SOCIALS (Outlined in circle) -->
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
  <circle cx="16" cy="16" r="3" stroke="#bfc2c6" stroke-width="2" fill="none" />
  <circle cx="21.2" cy="12.8" r="1" fill="#bfc2c6" />
  <symbol id="facebook" viewBox="0 0 32 32">
    <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" />
    <path d="M18.5 17.5h2l.5-3h-2.5v-1.5c0-.6.2-1 .8-1H21V9.5h-2c-2 0-2.5 1.2-2.5 2.5V14.5h-1.5v3h1.5V23h3v-5.5z"
      fill="#bfc2c6" />
  </symbol>
  </svg>
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
            <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="store.php">Store</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="keyboard.php">Keyboards</a>
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
        <div class="d-lg-flex col-lg-3 justify-content-lg-end">
          <!-- Search Icon -->
          <button class="btn btn-link p-2" style="box-shadow: none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" width="28" height="28">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
          </button>
          <!-- Handbag Icon -->
          <button class="btn btn-link p-2" style="box-shadow: none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" width="28" height="28">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 10.5V7.125A4.125 4.125 0 008.25 7.125V10.5M3.375 9.75l1.125 9A2.25 2.25 0 006.75 21h10.5a2.25 2.25 0 002.25-2.25l1.125-9a.75.75 0 00-.75-.75H4.125a.75.75 0 00-.75.75z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </nav>
  <div class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top "></div>
<!-- Body project -->
  <div class="section" style="width: 100%; padding: 15px 45px 15px 45px; background-color: #fff;">
    <h2>Keyboards</h2>
      <p class="subtitle">
        ค้นพบประสบการณ์การพิมพ์รูปแบบใหม่ ที่ผสานความแม่นยำ ความสบาย และความงดงามเข้าด้วยกัน<br />
        คีย์บอร์ดแต่บะรุ่นถูกออกแบบมาเพื่อมอบความรู้สึกเฉพาะตัว ทั้งสำหรับการทำงานและการเล่นเกม
      </p>
  </div>
  <div class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top "></div>

  <div class="button-container">
    <a href="keyboard.php" class="filter-btn ">Mechanical</a>
    <a href="membrane.php" class="filter-btn ">Membrane</a>
    <a href="gaming.php" class="filter-btn active">Gaming</a>
    <a href="ergonomic.php" class="filter-btn ">Ergonomic</a>
  </div>

 <section class="keyboard-section">
    <h2>Victory begins with your keyboard <span>— choose the one built for champions...</span></h2>

    <div class="product-container">
      <div class="product-card">
        <img src="keyboard/gaming/1.png" alt="Womier S-K80 75% RGB">
        <h3>Womier S-K80 75% RGB</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 1990 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/2.png" alt="Rk Royal Kludge RK290">
        <h3>RK Royal Kludge RK920</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 2090 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/3.png" alt="ASUS TUF Gaming K1">
        <h3>ASUS TUF Gaming K1</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 1090 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/4.png" alt="AULA F75 Gasket RGB">
        <h3>AULA F75 Gasket RGB</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 1590 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/5.png" alt="Gravastar Mercury K1">
        <h3>Gravastar Mercury K1</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 3990 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/6.png" alt="Royal Kludge R87">
        <h3>Royal Kludge R87</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 1390 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/7.png" alt="Pyro ROC12622">
        <h3>Pyro ROC12622</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 3790 BAHT</span>
      </div>

      <div class="product-card">
        <img src="keyboard/gaming/8.png" alt="MG108B Watermelon Edition">
        <h3>MG108B Watermelon Edition</h3>
        <p>รายละเอียด รายละเอียด รายละเอียด รายละเอียด</p>
        <span class="price">START 3890 BAHT</span>
      </div>
    </div>
  </section>


  </div>
<!-- End Body -->
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
</body>

</html>