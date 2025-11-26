<?php
session_start();
include 'config.php';

// Validate item ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid item ID.");
}

$item_id = intval($_GET['id']);

// Fetch item details
$stmt = $conn->prepare("
    SELECT item_id, name, description, rental_price, purchase_price, stock, image
    FROM items
    WHERE item_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();

// Image
$imagePath = (!empty($item['image']))
    ? "uploads/" . $item['image']
    : "https://via.placeholder.com/450x450?text=No+Image";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($item['name']); ?> - GOWN&GO</title>

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
        background: rgba(245, 230, 240, 0.35);
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

    .item-container {
        max-width: 1000px;
        margin: 40px auto;
        background: rgba(255,255,255,0.95);
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.12);
        padding: 40px; 
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .item-container img {
        width: 100%;
        border-radius: 12px;
        object-fit: cover;
    }

    .item-details h2 {
        margin-top: 0;
        font-family: 'Playfair Display', serif;
        color: #d86ca1;
        font-size: 2rem;
    }

    .cta {
        background-color: #d86ca1;
        color: #fff;
        padding: 12px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        margin-top: 15px;
    }
    .cta:hover {
        background-color: #b3548a;
    }

</style>
</head>
<body>

<header>
    <h1>GOWN&GO</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>

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

<div class="item-container">

    <div>
        <img src="<?php echo $imagePath; ?>" alt="">
    </div>

    <div class="item-details">

        <h2><?php echo htmlspecialchars($item['name']); ?></h2>

        <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>

        <p><strong>Rental Price:</strong> 
            <?php echo ($item['rental_price'] > 0) ? "₱" . number_format($item['rental_price'], 2) : "N/A"; ?>
        </p>

        <p><strong>Purchase Price:</strong> 
            <?php echo ($item['purchase_price'] > 0) ? "₱" . number_format($item['purchase_price'], 2) : "N/A"; ?>
        </p>

        <p><strong>Stock:</strong> <?php echo $item['stock']; ?></p>

        <?php if (!isset($_SESSION['user_id'])): ?>

            <a href="login.php" class="cta">Login to Order</a>

        <?php elseif ($_SESSION['role'] === 'customer'): ?>

            <form method="POST" action="client_home.php" style="margin-top:20px;">
                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                <input type="number" name="quantity" min="1" max="<?php echo $item['stock']; ?>" value="1"
                       style="padding:8px; width:60px; border-radius:8px; border:1px solid #ccc;">
                <button class="cta" type="submit" name="add_to_cart">Add to Cart</button>
            </form>

        <?php else: ?>

            <p>Admins cannot add to cart.</p>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
