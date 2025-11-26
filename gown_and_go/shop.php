<?php
session_start();
include 'config.php';

// Fetch ALL items
$items_q = mysqli_query($conn, "
    SELECT item_id, name, description, rental_price, purchase_price, stock, image
    FROM items
    ORDER BY created_at DESC
");

// Fetch first 3 featured products
$featured_q = mysqli_query($conn, "
    SELECT item_id, name, description, rental_price, purchase_price, image
    FROM items
    ORDER BY created_at DESC
    LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GOWN&GO - Shop</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #6b2b4a;
        position: relative;
    }
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(245,230,240,0.35);
        z-index: -1;
    }

    header {
        background: rgba(255,255,255,0.9);
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    header h1 {
        margin: 0;
        font-family: 'Playfair Display', serif;
        color: #d86ca1;
    }
    nav a {
        margin-left: 20px;
        color: #6b2b4a;
        font-weight: 600;
        text-decoration: none;
    }
    nav a:hover {
        color: #d86ca1;
    }

    .section-title {
        text-align: center;
        font-size: 2rem;
        margin-top: 40px;
        color: #d86ca1;
        font-family: 'Playfair Display', serif;
    }

    /* Featured Items */
    .featured-container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .featured-card {
        background: rgba(255,255,255,0.9);
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        text-align: center;
    }
    .featured-card img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        border-radius: 10px;
    }
    .featured-card h4 {
        margin: 10px 0 5px;
        font-family: 'Playfair Display', serif;
        color: #d86ca1;
    }

    /* Gown grid */
    .gown-container {
        max-width: 1100px;
        margin: 30px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        padding: 0 20px;
    }
    .gown-card {
        background: rgba(255,255,255,0.85);
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .gown-card img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        border-radius: 10px;
    }
    .gown-card h4 {
        color: #d86ca1;
        margin-top: 12px;
        font-family: "Playfair Display", serif;
    }

    .cta {
        background-color: #d86ca1;
        color: #fff;
        padding: 10px 18px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-block;
        margin-top: 12px;
        font-weight: bold;
    }
    .cta:hover {
        background-color: #b3548a;
    }

    /* About + Team Sections */
    .info-section {
        max-width: 1100px;
        margin: 60px auto;
        padding: 20px 40px;
        background: rgba(255,255,255,0.9);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.12);
    }
    .info-section h3 {
        font-family: 'Playfair Display', serif;
        color: #d86ca1;
        margin-top: 0;
        font-size: 1.7rem;
    }
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .team-card {
        background: rgba(255,255,255,0.95);
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .team-card h4 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 5px;
        color: #d86ca1;
    }

    footer {
        margin-top: 50px;
        background: #d86ca1;
        text-align: center;
        padding: 15px;
        color: #fff;
    }
</style>

</head>
<body>

<header>
    <h1>GOWN&GO</h1>
    <nav>
        <a href="index.php">Home</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin/dashboard.php">Admin Panel</a>
            <?php else: ?>
                <a href="client_home.php">Client Home</a>
            <?php endif; ?>
            <a href="logout.php" style="color:#b3548a;">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<!-- FEATURED PRODUCTS -->
<h2 class="section-title">Featured Gowns</h2>

<div class="featured-container">
<?php while($f = mysqli_fetch_assoc($featured_q)): ?>
    <div class="featured-card">
        <?php
        $img = (!empty($f['image'])) ? "uploads/" . $f['image'] :
        "https://via.placeholder.com/300x260?text=No+Image";
        ?>
        <img src="<?php echo $img; ?>">

        <h4><?php echo htmlspecialchars($f['name']); ?></h4>

        <p><?php echo substr(htmlspecialchars($f['description']), 0, 80) . "..."; ?></p>

        <a href="view_item.php?id=<?php echo $f['item_id']; ?>" class="cta">See Details</a>
    </div>
<?php endwhile; ?>
</div>

<!-- ABOUT THE PROJECT -->
<div class="info-section">
    <h3>About Gown&Go</h3>
    <p>
        Gown&Go is a simple and user-friendly rental and purchase platform dedicated to making
        formal wear accessible to everyone. Whether you're attending a wedding, a prom,
        a graduation, or a special occasion, our system ensures a smooth browsing and ordering experience.
    </p>
</div>

<!-- MEET THE TEAM -->
<div class="info-section">
    <h3>Meet the Team</h3>
    <p>Members of the Group IDK</p>

    <div class="team-grid">
        <div class="team-card">
            <h4>Batalla, Francheska Faith</h4>
        </div>
        <div class="team-card">
            <h4>Juarez, Annaliza</h4>
        </div>
        <div class="team-card">
            <h4>Lozano, Eunice</h4>
        </div>
        <div class="team-card">
            <h4>Rico, Donalen Grace</h4>
        </div>
    </div>
</div>

<footer>
    © <?php echo date("Y"); ?> GOWN&GO — Fashion for All.
</footer>

</body>
</html>
