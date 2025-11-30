<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: items.php");
    exit;
}

$item_id = (int) $_GET['id'];

// Load item
$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    header("Location: items.php");
    exit;
}

$name = $item['name'];
$description = $item['description'];
$rental_price = $item['rental_price'];
$purchase_price = $item['purchase_price'];
$stock = $item['stock'];
$status = $item['status'];
$current_image = $item['image'];

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $rental_price = (float) $_POST['rental_price'];
    $purchase_price = (float) $_POST['purchase_price'];
    $stock = (int) $_POST['stock'];
    $status = $_POST['status'];

    if ($name === "" || $stock < 0) {
        $error = "Please fill in required fields properly.";
    } else {
        $new_image = $current_image;

        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $original = basename($_FILES['image']['name']);
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];

            if (in_array($ext, $allowed)) {
                $new_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $original);
                $targetFile = $targetDir . $new_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    // Optionally delete old image
                    if (!empty($current_image) && file_exists($targetDir . $current_image)) {
                        @unlink($targetDir . $current_image);
                    }
                    $new_image = $new_name;
                } else {
                    $error = "Failed to upload new image.";
                }
            } else {
                $error = "Invalid image type. Allowed: jpg, jpeg, png, gif, webp.";
            }
        }

        if ($error === "") {
            $stmt_upd = $conn->prepare("
                UPDATE items 
                SET name = ?, description = ?, rental_price = ?, purchase_price = ?, stock = ?, image = ?, status = ?
                WHERE item_id = ?
            ");
            $stmt_upd->bind_param(
                "ssddissi",
                $name,
                $description,
                $rental_price,
                $purchase_price,
                $stock,
                $new_image,
                $status,
                $item_id
            );

            if ($stmt_upd->execute()) {
                $success = "Item updated successfully.";
                $current_image = $new_image;
            } else {
                $error = "Database error while updating item.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Item - GOWN&GO Admin</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="inclusion/stylesheet.css">

<style>
  .main-container {max-width: 800px;}
  .msg-error {
    background: #ffe0e0;
    color: #b30000;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
  }
  .msg-success {
    background: #e8ffe8;
    color: #2d8a3d;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
  }
  .preview-img {
    margin-top: 8px;
    width: 120px;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
  }
</style>
</head>

<body>

<?php include 'inclusion/nav.php'; ?>

<main class="main-container">
  <h2>Edit Item</h2>

  <?php if (!empty($error)): ?>
    <div class="msg-error"><?php echo $error; ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="msg-success"><?php echo $success; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Item Name *</label>
    <input type="text" name="name" required value="<?php echo htmlspecialchars($name); ?>">

    <label>Description</label>
    <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>

    <label>Rental Price (₱)</label>
    <input type="number" step="0.01" name="rental_price" value="<?php echo htmlspecialchars($rental_price); ?>">

    <label>Purchase Price (₱)</label>
    <input type="number" step="0.01" name="purchase_price" value="<?php echo htmlspecialchars($purchase_price); ?>">

    <label>Stock *</label>
    <input type="number" name="stock" min="0" required value="<?php echo htmlspecialchars($stock); ?>">

    <label>Status</label>
    <select name="status">
      <option value="active" <?php echo ($status === 'active' ? 'selected' : ''); ?>>Active</option>
      <option value="inactive" <?php echo ($status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
    </select>

    <label>Current Image</label>
    <br>
    <?php
      $img = !empty($current_image)
        ? "../uploads/" . $current_image
        : "https://via.placeholder.com/120x140?text=No+Image";
    ?>
    <img class="preview-img" src="<?php echo $img; ?>" alt="Current image">

    <label>Change Image</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit" class="btn">Update Item</button>
  </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
