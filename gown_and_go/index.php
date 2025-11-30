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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
    <style>
        
        html, body {
            margin: 0; padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #6b2b4a;
            line-height: 1.6;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(245, 230, 240, 0.30);
            z-index: -1;
        }

        .page-wrapper {
            padding-top: 90px; 
        }

        header {
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            color: #d86ca1;
            text-shadow: 0 0 3px #b3548a;
        }
        h1 {
            font-size: 3em;
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 1.4em;
            font-weight: 300;
            margin-top: 8px;
            color: #b3548a;
        }

        section {
            background: rgba(245, 230, 240, 0.50);
            max-width: 800px;
            margin: 40px auto;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(183, 134, 154, 0.3);
            color: #6b2b4a;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            justify-items: center;
        }
        .feature {
            background-color: rgba(245, 230, 240, 0.85);
            border-radius: 10px;
            padding: 25px 20px;
            width: 100%;
            max-width: 260px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(183, 134, 154, 0.15);
            transition: transform 0.3s ease;
        }
        .feature:hover {
            transform: translateY(-8px);
        }

        .cta {
            background-color: #d86ca1;
            color: #f5e6f0;
            padding: 14px 36px;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            cursor: pointer;
            margin: 15px 8px;
            transition: background-color 0.3s ease;
        }
        .cta:hover {
            background-color: #b3548a;
        }

        footer {
            background-color: #d86ca1;
            color: #f5e6f0;
            text-align: center;
            padding: 20px 15px 0 15px;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            section { margin: 30px 15px; }
            .features { grid-template-columns: 1fr; }
            h1 { font-size: 2.4em; }
            .subtitle { font-size: 1.1em; }
        }
   
    </style>
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
