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
    <title>Admin — Gaming Store</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/index.css" />
  </head>
  <body>
    <?php include "../components/admin_header.php" ?>

    <main class="featured-banner-container">
      <h1 class="featured-product-title">Admin Dashboard</h1>
      <p>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>.</p>
      <p>This is the main admin page. Use the navigation above to manage inventory or review purchase history.</p>
    </main>
  </body>
</html>
