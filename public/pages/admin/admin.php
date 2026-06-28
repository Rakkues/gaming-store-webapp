<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../src/config/database.php';

$pdo = getDBConnection();
$productCount = (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$memberCount = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin — Gaming Store</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/index.css" />
  </head>
  <body>
    <?php include "../components/admin_header.php" ?>

    <main class="admin-page">
      <h1 class="featured-product-title">Admin Dashboard</h1>
      <p>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>.</p>

      <div class="admin-dashboard-cards">
        <a class="item admin-dashboard-card" href="inventory.php">
          <div class="admin-card-count"><?= htmlspecialchars((string) $productCount) ?></div>
          <div class="item-description">
            <h3 class="item-name">Inventory</h3>
            <p><?= $productCount === 1 ? '1 product' : htmlspecialchars((string) $productCount) . ' products' ?></p>
            <p class="admin-muted">Manage product stock, prices, and details.</p>
          </div>
        </a>

        <a class="item admin-dashboard-card" href="orders/order_history.php">
          <div class="admin-card-count"><?= htmlspecialchars((string) $orderCount) ?></div>
          <div class="item-description">
            <h3 class="item-name">Orders</h3>
            <p><?= $orderCount === 1 ? '1 purchase record' : htmlspecialchars((string) $orderCount) . ' purchase records' ?></p>
            <p class="admin-muted">Review orders and purchased items.</p>
          </div>
        </a>

        <a class="item admin-dashboard-card" href="members.php">
          <div class="admin-card-count"><?= htmlspecialchars((string) $memberCount) ?></div>
          <div class="item-description">
            <h3 class="item-name">Members</h3>
            <p><?= $memberCount === 1 ? '1 user' : htmlspecialchars((string) $memberCount) . ' users' ?></p>
            <p class="admin-muted">View customers and admin accounts.</p>
          </div>
        </a>
      </div>
    </main>
  </body>
</html>
