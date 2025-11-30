<?php
session_start();
include 'config.php';

$login_error = "";
$login_success = "";

// If the login form is submitted, fetch and verify 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if (mysqli_num_rows($query) == 1) {
        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['password'])) {

            // Store session variables
            $_SESSION['user_id']  = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];

            $login_success = "Login successful! Welcome, " . $row['username'] . ".";
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Gown&Go</title>
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  
  <!-- ========================= Styling ========================= -->
  <style>
    * { box-sizing: border-box; }
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #6b2b4a;
      position: relative;
    }
    a { color: #d86ca1; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .container {
      max-width: 900px;
      height: 600px;
      margin: 40px auto;
      display: flex;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(183, 134, 154, 0.3);
      background: rgba(245, 230, 240, 0.85);
      overflow: hidden;
    }
    .left-panel {
      flex: 1;
      padding: 60px 50px;
      background: rgba(216, 108, 161, 0.1);
      color: #6b2b4a;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .left-panel h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.75rem;
      font-weight: 700;
      line-height: 1.1;
      margin: 0;
      color: #d86ca1;
    }
    .right-panel {
      flex: 1;
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: rgba(255, 255, 255, 0.9);
    }
    .right-panel h2 {
      margin-top: 0;
      margin-bottom: 8px;
      font-weight: 700;
      font-size: 1.5rem;
      color: #6b2b4a;
    }
    .right-panel p.subtitle {
      font-size: 0.9rem;
      margin-top: 0;
      margin-bottom: 28px;
      color: #666;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    input[type="email"],
    input[type="password"] {
      padding: 12px 15px;
      font-size: 1rem;
      border: 1.8px solid #ddd;
      border-radius: 6px;
      transition: border-color 0.3s;
    }
    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: #d86ca1;
      box-shadow: 0 0 6px rgba(216, 108, 161, 0.3);
    }
    button.submit-btn {
      background-color: #d86ca1;
      color: white;
      font-weight: 700;
      font-size: 1.1rem;
      padding: 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 12px;
    }
    button.submit-btn:hover {
      background-color: #b3548a;
    }
    .register-link {
      text-align: center;
      margin-top: 20px;
      font-size: 0.9rem;
      color: #666;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        height: auto;
        margin: 20px;
      }
      .left-panel, .right-panel {
        flex: none;
        padding: 40px 30px;
      }
    }
  </style>
  </head>

<body class="bg-light">

  <main class="container">
    <section class="left-panel">
      <h1>Elegant Gowns<br>for Everyone.</h1>
    </section>

    <section class="right-panel"> <!-- Login Form -->
      <h2>Login to Your Account</h2>
      <p class="subtitle">Enter your email and password to access your account.</p>

      <form action="" method="POST">    
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn submit-btn" type="submit">Login</button>
      </form>

      <hr>
      <p class="text-center">
          New user? <a href="register.php">Create an account</a>.
      </p>

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Login Error</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php echo $login_error; ?>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div class="modal fade" id="successModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Login Successful</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php echo $login_success; ?>
            <br><small>You will be redirected shortly...</small>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js
"></script>

    <!-- Show modal messages -->
    <?php
    // Show error modal
    if (!empty($login_error)) {
        echo "<script>
                var e = new bootstrap.Modal(document.getElementById('errorModal'));
                e.show();
              </script>";
    }

    // Show success modal + redirect after 1.5 seconds
    if (!empty($login_success)) {
        echo "<script>
                var s = new bootstrap.Modal(document.getElementById('successModal'));
                s.show();
                setTimeout(function() {
                    window.location.href = '" . 
                    ($_SESSION['role'] == "admin" ? "admin/dashboard.php" : "client_home.php") . 
                    "';
                }, 1500);
              </script>";
    }
    ?>
</body>
</html>
