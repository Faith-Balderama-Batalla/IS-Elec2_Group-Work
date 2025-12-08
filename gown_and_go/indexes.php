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
    <link rel="stylesheet" href="inclusion/stylesheet.css">

    <style>
        header {
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            color: #c8538cff;
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
            <a href="login.php"><button class="cta">Login</button></a>
            <a href="shop.php"><button class="cta">Browse Collection</button></a>
        </div>
    </section>

</body>
</html>
