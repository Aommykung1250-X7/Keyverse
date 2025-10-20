<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KEYVERSE - Sign Up</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    /* ... (โค้ด CSS ของคุณทั้งหมด) ... */
    body { color: #4d4c51; }
    .signup-section { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 0; }
    .signup-section h1 { font-size: 2rem; font-weight: 700; color: #4a4747; margin-bottom: 60px; letter-spacing: 1px; }
    .signup-form { display: flex; flex-direction: column; align-items: center; gap: 25px; }
    .signup-form .input-box { display: flex; align-items: center; background-color: #d9d9d9; border-radius: 50px; padding: 14px 20px; width: 480px; max-width: 90%; box-sizing: border-box; }
    .signup-form .input-box i { color: #4a4a4a; font-size: 18px; margin-right: 14px; }
    .signup-form .input-box input { border: none; background: none; outline: none; width: 100%; font-size: 16px; color: #333; font-family: "Poppins", sans-serif; }
    .signup-btn { background-color: #3f3e3e; color: #fff; border: none; border-radius: 50px; padding: 16px 80px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 15px; transition: 0.3s; }
    .signup-btn:hover { background-color: #2e2d2d; }
    .login-text { margin-top: 25px; font-size: 15px; color: #4a4a4a; font-family: "Poppins", sans-serif; }
    .login-text a { text-decoration: none; color: #333; font-weight: 600; }
    .message { width: 480px; max-width: 90%; padding: 15px; margin-bottom: 20px; border-radius: 10px; font-size: 16px; text-align: center; }
    .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    /* ... (CSS ส่วนอื่นๆ ของคุณ) ... */
  </style>
  <svg xmlns="http://www.w3.org/2000/svg" style="display:none;"><symbol id="instagram" viewBox="0 0 32 32"> <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" /> <rect x="10" y="10" width="12" height="12" rx="4" stroke="#bfc2c6" stroke-width="2" fill="none" /> <circle cx="16" cy="16" r="3" stroke="#bfc2c6" stroke-width="2" fill="none" /> <circle cx="21.2" cy="12.8" r="1" fill="#bfc2c6" /> </symbol><symbol id="facebook" viewBox="0 0 32 32"> <circle cx="16" cy="16" r="15" stroke="#bfc2c6" stroke-width="2" fill="none" /> <path d="M18.5 17.5h2l.5-3h-2.5v-1.5c0-.6.2-1 .8-1H21V9.5h-2c-2 0-2.5 1.2-2.5 2.5V14.5h-1.5v3h1.5V23h3v-5.5z" fill="#bfc2c6" /> </symbol></svg>
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary rounded" aria-label="Thirteenth navbar example"><div class="container-fluid"> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample11" aria-controls="navbarsExample11" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button> <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11"> <a class="navbar-brand col-lg-3 me-0" style="padding-left: 18px; color: #4d4c51" href="index.php">KEYVERSE</a> <ul class="navbar-nav col-lg-6 justify-content-lg-center"> <li class="nav-item"> <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="store.html">Store</a> </li> <li class="nav-item"> <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="keyboard.html">Keyboards</a> </li> <li class="nav-item"> <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="switches.html">Switches</a> </li> <li class="nav-item"> <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="keycap.html">Keycaps</a> </li> <li class="nav-item"> <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="accessories.html">Accessories</a> </li> <li class="nav-item"> <a class="nav-link" style="color: #4d4c51; font-weight: 400" href="modding.html">DIY / Modding</a> </li> </ul> <div class="d-lg-flex col-lg-3 justify-content-lg-end"> </div> </div> </div></nav>
  <div class="d-flex flex-wrap justify-content-between align-items-center py-2 my-1 border-top "></div>

  <section class="signup-section">
  <h1>SIGN UP</h1>
  <?php
    if (isset($_SESSION['message'])) {
      $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'error';
      echo '<div class="message ' . $message_type . '">' . $_SESSION['message'] . '</div>';
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
    }
  ?>
  <form class="signup-form" action="signup_process.php" method="POST">
    <div class="input-box"> <i class="fa-solid fa-user"></i> <input type="text" placeholder="Username" name="username" required> </div>
    <div class="input-box"> <i class="fa-solid fa-envelope"></i> <input type="email" placeholder="E - mail" name="email" required> </div>
    <div class="input-box"> <i class="fa-solid fa-lock"></i> <input type="password" placeholder="Password" name="password" required> </div>
    <div class="input-box"> <i class="fa-solid fa-lock"></i> <input type="password" placeholder="Confirm Password" name="confirm_password" required> </div>
    <button type="submit" class="signup-btn">CREATE AN ACCOUNT</button>
    <p class="login-text"> Already have an account ? <a href="login.php">Login here</a> </p>
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