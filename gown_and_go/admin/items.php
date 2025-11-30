<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$items = [];
$res = $conn->query("SELECT * FROM items ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Items - GOWN&GO Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="inclusion/stylesheet.css">

<style>
    img.thumb {
      width: 60px;
      height: 70px;
      object-fit: cover;
      border-radius: 6px;
    }
</style>
</head>
<body>

  <?php include 'inclusion/nav.php'; ?>

  <main class="main-container">
    <h2>Inventory Items</h2>

    <a href="add_item.php" class="btn">+ Add New Item</a>

    <?php if (empty($items)): ?>
      <p>No items found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Rental (₱)</th>
            <th>Purchase (₱)</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td>
                <?php
                  $img = !empty($it['image'])
                      ? "../uploads/" . $it['image']
                      : "https://via.placeholder.com/60x70?text=No+Image";
                ?>
                <img class="thumb" src="<?php echo $img; ?>" alt="">
              </td>
              <td><?php echo htmlspecialchars($it['name']); ?></td>
              <td><?php echo (int)$it['stock']; ?></td>
              <td><?php echo number_format($it['rental_price'], 2); ?></td>
              <td><?php echo number_format($it['purchase_price'], 2); ?></td>
              <td>
                <a class="btn" href="edit_item.php?id=<?php echo $it['item_id']; ?>">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
