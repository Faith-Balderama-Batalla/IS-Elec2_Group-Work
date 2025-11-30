<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$name = $description = $status = "";
$rental_price = $purchase_price = $stock = 0;
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
        // Handle image upload
        $image_name = null;

        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $original = basename($_FILES['image']['name']);
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];

            if (in_array($ext, $allowed)) {
                $image_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $original);
                $targetFile = $targetDir . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image type. Allowed: jpg, jpeg, png, gif, webp.";
            }
        }

        if ($error === "") {
            $stmt = $conn->prepare("
                INSERT INTO items (name, description, rental_price, purchase_price, stock, image, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param(
                "ssddiss",
                $name,
                $description,
                $rental_price,
                $purchase_price,
                $stock,
                $image_name,
                $status
            );

            if ($stmt->execute()) {
                $success = "Item added successfully.";
                // Reset form
                $name = $description = "";
                $rental_price = $purchase_price = 0;
                $stock = 0;
                $status = "active";
            } else {
                $error = "Database error while adding item.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Item - GOWN&GO Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="inclusion/stylesheet.css">

  <style>
    .main-container {max-width:800px; }
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
  </style>
</head>

<body>

  <?php include 'inclusion/nav.php'; ?>

  <main class="main-container">
    <h2>Add New Item</h2>

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
        <option value="active" <?php echo ($status === 'inactive' ? '' : 'selected'); ?>>Active</option>
        <option value="inactive" <?php echo ($status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
      </select>

      <label>Image</label>
      <input type="file" name="image" accept="image/*">

      <button type="submit" class="btn">Save Item</button>
    </form>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>