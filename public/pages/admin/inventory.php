<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../src/config/database.php';

$pdo = getDBConnection();
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$stockStatus = trim($_GET['stock_status'] ?? '');

$allowedCategories = ['mouse', 'keyboard', 'audio', 'collectibles', 'merchandise'];
$allowedStockStatuses = ['in_stock', 'low_stock', 'out_of_stock'];

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(name LIKE :search_name OR description LIKE :search_description)';
    $params[':search_name'] = '%' . $search . '%';
    $params[':search_description'] = '%' . $search . '%';
}

if ($category !== '' && in_array($category, $allowedCategories, true)) {
    $where[] = 'category = :category';
    $params[':category'] = $category;
} else {
    $category = '';
}

if ($stockStatus !== '' && in_array($stockStatus, $allowedStockStatuses, true)) {
    if ($stockStatus === 'in_stock') {
        $where[] = 'stock > 5';
    } elseif ($stockStatus === 'low_stock') {
        $where[] = 'stock BETWEEN 1 AND 5';
    } elseif ($stockStatus === 'out_of_stock') {
        $where[] = 'stock = 0';
    }
} else {
    $stockStatus = '';
}

$query = "
    SELECT id, name, description, price, stock, category, image_path, created_at
    FROM products
";

if (!empty($where)) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}

$query .= ' ORDER BY id DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventory — Gaming Store Admin</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/index.css" />
  </head>
  <body>
    <?php include "../components/admin_header.php" ?>

    <main class="admin-page">
      <h1 class="featured-product-title">Inventory</h1>
      <p>View all products currently stored in the database.</p>

      <form class="admin-filter-form" method="GET" action="inventory.php">
        <div class="admin-filter-field">
          <label for="search">Search</label>
          <input
            type="text"
            id="search"
            name="search"
            placeholder="Product name or description"
            value="<?= htmlspecialchars($search) ?>"
          />
        </div>

        <div class="admin-filter-field">
          <label for="category">Category</label>
          <select id="category" name="category">
            <option value="">All categories</option>
            <?php foreach ($allowedCategories as $option) : ?>
              <option value="<?= htmlspecialchars($option) ?>" <?= $category === $option ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($option)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="admin-filter-field">
          <label for="stock_status">Stock</label>
          <select id="stock_status" name="stock_status">
            <option value="">All stock</option>
            <option value="in_stock" <?= $stockStatus === 'in_stock' ? 'selected' : '' ?>>In stock</option>
            <option value="low_stock" <?= $stockStatus === 'low_stock' ? 'selected' : '' ?>>Low stock</option>
            <option value="out_of_stock" <?= $stockStatus === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
          </select>
        </div>

        <div class="admin-filter-actions">
          <button type="submit">Apply</button>
          <a href="inventory.php">Reset</a>
        </div>
      </form>

      <p class="admin-muted"><?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?> found.</p>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)) : ?>
              <tr>
                <td colspan="7">No products found.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($products as $product) : ?>
              <tr>
                <td><?= htmlspecialchars((string) $product['id']) ?></td>
                <td>
                  <img
                    class="admin-product-img"
                    src="/gaming-store-webapp/public<?= htmlspecialchars($product['image_path']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                  />
                </td>
                <td>
                  <strong><?= htmlspecialchars($product['name']) ?></strong>
                  <p class="admin-muted"><?= htmlspecialchars($product['description'] ?? '') ?></p>
                </td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td>RM <?= htmlspecialchars(number_format((float) $product['price'], 2)) ?></td>
                <td><?= htmlspecialchars((string) $product['stock']) ?></td>
                <td><?= htmlspecialchars($product['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>
