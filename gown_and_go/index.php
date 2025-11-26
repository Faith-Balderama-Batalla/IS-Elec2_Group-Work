<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gown&Go</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
</head>

<body>

<!-- ========================= NAVBAR ========================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><strong>Gown&Go</strong></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="client_home.php">
                            Welcome, <?= $_SESSION['username']; ?>
                        </a>
                    </li>

                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>

                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="page-wrapper">
    
<!-- ========================= details ========================= -->
    <header>
        <div>
            <h1>Gown & Go</h1>
            <p class="subtitle">Elegance for Every Occasion</p>
        </div>
    </header>

    <section>
        <h2>Experience the Perfect Fit</h2>

        <div class="features">
            <div class="feature"><h3>Rent</h3><p>Affordable rentals for special events.</p></div>
            <div class="feature"><h3>Purchase</h3><p>Own your dream gown.</p></div>
            <div class="feature"><h3>Customize</h3><p>Tailor sizing and adjustments.</p></div>
            <div class="feature"><h3>Quality</h3><p>Premium gowns for all occasions.</p></div>
        </div>

        <div style="text-align:center; margin-top: 20px;">
            <a href="register.php"><button class="cta">Create Account</button></a>
            <a href="shop.php"><button class="cta">Browse Collection</button></a>
        </div>

    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js
"></script>
</body>
</html>
