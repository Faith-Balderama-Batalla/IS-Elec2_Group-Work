<?php
session_start();
include 'config.php';

$register_error = "";
$register_success = "";

// If user submits the form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address  = trim($_POST['address']);
    $contact  = trim($_POST['contact']);
    $role     = "customer";

    // 1. Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        $register_error = "This email is already registered.";
    } else {

        // 2. Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. Insert user
        $sql = "INSERT INTO users (username, email, password, address, contact_no, role)
                VALUES ('$username', '$email', '$hashed_password', '$address', '$contact', '$role')";

        if (mysqli_query($conn, $sql)) {
            // Registration successful
            $register_success = "Account created successfully!";
        } else {
            $register_error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gown&Go</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
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
    .error-message {
      color: #d9534f;
      font-size: 0.9rem;
      margin-bottom: 18px;
    }
    .success-message {
      color: #5cb85c;
      font-size: 0.9rem;
      margin-bottom: 18px;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea {
      padding: 12px 15px;
      font-size: 1rem;
      border: 1.8px solid #ddd;
      border-radius: 6px;
      transition: border-color 0.3s;
      resize: vertical;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    textarea:focus {
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
    .login-link {
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
        <div class="left-panel">
            <h1>Welcome to Gown&Go</h1>
            <p>Your journey to elegance begins here. Create an account to explore our exquisite collection of gowns and accessories.</p>
        </div>
        <div class="right-panel">
            <h2>Create Account</h2>
            <?php if (!empty($register_error)): ?>
                <div class="error-message"><?php echo $register_error; ?></div>
            <?php endif; ?>
            <?php if (!empty($register_success)): ?>
                <div class="success-message"><?php echo $register_success; ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <textarea name="address" rows="2" placeholder="Address" required></textarea>
                <input type="text" name="contact" placeholder="Contact Number" required>
                <button class="submit-btn" type="submit">Register</button>
            </form>

            <div class="login-link">Already have an account? <a href="login.php">Login Here.</a></div>
        </div>
    </main>
           

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Registration Error</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php echo $register_error; ?>
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
                    <h5 class="modal-title">Registration Successful</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php echo $register_success; ?><br>
                    You can now <a href="login.php">login</a>.
                </div>
                <div class="modal-footer">
                    <a href="login.php" class="btn btn-success">Go to Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js
"></script>

    <!-- Show modal depending on error/success -->
    <?php
        if (!empty($register_error)) {
        echo "<script>
            var e = new bootstrap.Modal(document.getElementById('errorModal'));
            e.show();
            </script>";
        }

        if (!empty($register_success)) {
        echo "<script>
            var s = new bootstrap.Modal(document.getElementById('successModal'));
            s.show();
            </script>";
        }
    ?>

</body>
</html>
