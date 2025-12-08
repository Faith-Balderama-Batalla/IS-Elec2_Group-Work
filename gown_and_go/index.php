<?php
session_start();
include 'config.php';

// Fetch 3 featured gowns
$featured = $conn->query("
    SELECT item_id, name, image, description, rental_price, purchase_price
    FROM items
    ORDER BY created_at DESC
    LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GOWN&GO - Home</title>

<link rel="stylesheet" href="stylesheet.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #fff;
    color: #6b2b4a;
}

/* ============ HERO WRAP ============ */
.hero-wrap {
    background: url('https://i.pinimg.com/1200x/63/01/8a/63018a11c5ad770ed2eec2d2587cea74.jpg') no-repeat center center/cover;
    padding: 80px 20px;
    display: flex;
    justify-content: center;
}

.hero-panel {
    max-width: 1100px;
    width: 100%;
    background: rgba(255,255,255,0.78);
    border-radius: 18px;
    box-shadow: 0 10px 35px rgba(0,0,0,0.25);
    padding: 35px 40px 40px;
    text-align: center;
}

/* Title inside panel */
.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.4rem;
    margin-bottom: 25px;
    color: #6b2b4a;
}

/* ============ FEATURE CARDS ============ */
.features-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 18px;
    margin-bottom: 25px;
}

.feature-card {
    flex: 1 1 200px;
    max-width: 230px;
    background: rgba(255,255,255,0.9);
    border-radius: 14px;
    padding: 15px 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.feature-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    margin-bottom: 6px;
    color: #c24888;
}

.feature-card p {
    font-size: 0.9rem;
    margin: 0;
}

/* Buttons under feature cards */
.hero-buttons {
    margin-top: 10px;
    margin-bottom: 25px;
}

.hero-buttons a {
    display: inline-block;
    margin: 0 8px 8px;
    padding: 9px 22px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    background: #d86ca1;
    color: #fff;
    border: none;
    transition: background 0.2s;
}

.hero-buttons a:hover {
    background: #b3548a;
}

/* ============ FEATURED GOWNS ============ */
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.9rem;
    margin: 10px 0 15px;
    color: #d86ca1;
}

.featured-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 18px;
    margin-top: 10px;
}

.featured-card {
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    text-align: left;
}

.featured-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 10px;
}

.featured-card h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    color: #c24888;
    margin: 8px 0 4px;
}

.featured-card p {
    font-size: 0.9rem;
    margin-bottom: 6px;
}

.view-link {
    display: inline-block;
    margin-top: 4px;
    font-size: 0.9rem;
    text-decoration: none;
    color: #d86ca1;
    font-weight: 600;
}

/* ============ ABOUT + TEAM SECTION ============ */
.info-section {
    background: linear-gradient(to bottom, #F8D7E8, #F3E6FF);
    padding: 50px 20px 40px;
}

.info-inner {
    max-width: 1100px;
    margin: auto;
    background: #fff;
    padding: 35px 40px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(183,134,154,0.3);
}

.info-inner h3 {
    font-family: 'Playfair Display', serif;
    color: #d86ca1;
    font-size: 1.7rem;
    margin-top: 0;
    margin-bottom: 8px;
}

.info-inner p {
    font-size: 0.95rem;
    line-height: 1.6;
}

/* team */
.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
    gap: 18px;
    margin-top: 22px;
}

.team-card {
    background: rgba(255,255,255,0.95);
    padding: 16px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.team-card h4 {
    font-family: 'Playfair Display', serif;
    color: #c24888;
    margin: 0;
}

/* footer */
footer {
    background: #d86ca1;
    padding: 14px;
    text-align: center;
    color: #fff;
    font-size: 0.9rem;
}

/* responsiveness */
@media (max-width: 768px) {
    .hero-panel {
        padding: 25px 18px 30px;
    }
    .hero-title {
        font-size: 2rem;
    }
}
</style>

</head>
<body>

<!-- HERO WRAP: background + translucent panel -->
<section class="hero-wrap">
    <div class="hero-panel">
        <h1 class="hero-title">Gown & Go</h1>
        <h3 class="subtitle">Elegance for Every Occasion</h3>

        <!-- 4 feature cards in one horizontal line -->
        <div class="features-row">
            <div class="feature-card">
                <h3>Rent</h3>
                <p>Affordable rentals for special events.</p>
            </div>
            <div class="feature-card">
                <h3>Purchase</h3>
                <p>Own your dream gown for keeps.</p>
            </div>
            <div class="feature-card">
                <h3>Customize</h3>
                <p>Tailor sizing and adjustments to your style.</p>
            </div>
            <div class="feature-card">
                <h3>Quality</h3>
                <p>Premium gowns and dresses for all occasions.</p>
            </div>
        </div>

        <!-- Featured gowns INSIDE same panel -->
        <h2 class="section-title">Featured Gowns</h2>
        <div class="featured-container">
            <?php while($f = $featured->fetch_assoc()): ?>
                <?php
                    $img = !empty($f['image']) ? "uploads/".$f['image']
                          : "https://via.placeholder.com/300x220?text=No+Image";
                ?>
                <div class="featured-card">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($f['name']); ?>">
                    <h4><?php echo htmlspecialchars($f['name']); ?></h4>
                    <p><?php echo substr(htmlspecialchars($f['description']), 0, 80) . "..."; ?><br>Login to see details.</p>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- buttons -->
        <div class="hero-buttons">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php">Create Account</a>
                <a href="login.php">Login</a>
            <?php else: ?>
                <a href="client_home.php">My Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- BELOW THE FRAME: only About & Team -->
<section class="info-section">
    <div class="info-inner">
        <h3>About Gown&Go</h3>
        <p>
            Gown&Go is a simple and user-friendly rental and purchase platform dedicated to making
            formal wear accessible to everyone. Whether you're attending a wedding, prom, graduation,
            or a special celebration, our system lets you discover, reserve, and manage gowns with ease.
        </p>

        <h3 style="margin-top:28px;">Meet the Team</h3>
        <div class="team-grid">
            <div class="team-card"><h4>Batalla, Francheska Faith</h4></div>
            <div class="team-card"><h4>Juarez, Annaliza</h4></div>
            <div class="team-card"><h4>Lozano, Eunice</h4></div>
            <div class="team-card"><h4>Rico, Donalen Grace</h4></div>
        </div>
    </div>
</section>

<footer>
    © <?php echo date("Y"); ?> GOWN&GO — Fashion for All.
</footer>

</body>
</html>
