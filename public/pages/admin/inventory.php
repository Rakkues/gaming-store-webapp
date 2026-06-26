<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}
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

    <main class="featured-banner-container">
      <h1 class="featured-product-title">Inventory</h1>
      <p>This page is for managing products, stock levels, prices, categories, and product images.</p>
      <p>Product management tools can be added here later.</p>
    </main>
  </body>
</html>
