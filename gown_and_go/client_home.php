<?php
session_start();
include 'config.php';

// Fetch items
$items_q = mysqli_query($conn, "
    SELECT item_id, name, description, rental_price, purchase_price, stock, image
    FROM items
    ORDER BY created_at DESC
");

// Handle Add to Cart
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        header("Location: login.php");
        exit;
    }

    $item_id = (int) $_POST['item_id'];
    $order_type = $_POST['order_type'];
    $quantity = max(1, (int) $_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][$item_id] = [
        "qty" => $quantity,
        "type" => $order_type
    ];

    $message = "Item added to cart!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Client Home - Gown&Go</title>

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
        font-family: 'Playfair Display', serif;
        margin: 0;
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

    .message {
        background: #e6ffe6;
        color: #3c763d;
        border: 1px solid #a3d7a3;
        width: 50%;
        margin: 20px auto;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
    }

    .gown-container {
        max-width: 1100px;
        margin: 30px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        padding: 0 20px;
    }

    .gown-card {
        background: rgba(255,255,255,0.88);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .gown-card img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        border-radius: 10px;
    }
    .gown-card h3 {
        margin-top: 10px;
        color: #d86ca1;
        font-family: 'Playfair Display', serif;
    }

    select, input[type=number] {
        width: 90%;
        padding: 6px;
        margin-top: 6px;
        border-radius: 6px;
        border: 1px solid #aaa;
    }

    .add-btn {
        margin-top: 10px;
        padding: 8px 14px;
        background: #d86ca1;
        border: none;
        color: #fff;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;
    }
    .add-btn:hover {
        background: #b3548a;
    }
</style>
</head>

<body>

<header>
    <h1>GOWN&GO</h1>

    <nav>
        <a href="client_home.php">Shop</a>
        <a href="cart.php">Cart</a>
        <a href="orders.php">My Orders</a>
        <a href="logout.php" style="color:#b3548a;">Logout</a>
    </nav>
</header>

<?php if (!empty($message)): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<div class="gown-container">

<?php while ($item = mysqli_fetch_assoc($items_q)): ?>
    <?php
        $imagePath = (!empty($item['image']))
            ? "uploads/" . $item['image']
            : "https://via.placeholder.com/300x260?text=No+Image";
    ?>

    <div class="gown-card">
        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">

        <h3><?php echo htmlspecialchars($item['name']); ?></h3>

        <p>
            <strong>Rental:</strong> ₱<?php echo number_format($item['rental_price'], 2); ?><br>
            <strong>Purchase:</strong> ₱<?php echo number_format($item['purchase_price'], 2); ?>
        </p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" style="color:#d86ca1;">Login to order</a>
        <?php else: ?>

        <form method="POST">
            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">

            <select name="order_type" required>
                <option value="Purchase">Purchase</option>
                <option value="Rental">Rental</option>
            </select>

            <input type="number" name="quantity" min="1" value="1" required>

            <button type="submit" name="add_to_cart" class="add-btn">Add to Cart</button>
        </form>

        <?php endif; ?>
    </div>

<?php endwhile; ?>

</div>

</body>
</html>
