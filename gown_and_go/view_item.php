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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="inclusion/stylesheet.css">

<style>
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
    <?php include 'inclusion/nav.php'; ?>

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
    <input type="hidden" name="item_id" value="<?= $item['item_id']; ?>">

    <select name="order_type" required
        style="padding:8px; border-radius:8px; border:1px solid #ccc; margin-bottom:8px;">
        <option value="Purchase">Purchase</option>
        <option value="Rental">Rental</option>
    </select><br>

    <input type="number" name="quantity" min="1" max="<?= $item['stock']; ?>" value="1"
        style="padding:8px; width:70px; border-radius:8px; border:1px solid #ccc;">

    <button class="cta" type="submit" name="add_to_cart">Add to Cart</button>
</form>


        <?php else: ?>

            <p>Admins cannot add to cart.</p>

        <?php endif; ?>

    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
