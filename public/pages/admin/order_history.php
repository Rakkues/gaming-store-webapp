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
    <title>Order History — Gaming Store Admin</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/index.css" />
  </head>
  <body>
    <?php include "../components/admin_header.php" ?>

    <main class="featured-banner-container">
      <h1 class="featured-product-title">Order History</h1>
      <p>This page is for viewing purchase logs, completed orders, transaction IDs, and customer order details.</p>
      <p>Order history tables and filters can be added here later.</p>
    </main>
  </body>
</html>
