<?php
session_start();
$isLoggedIn = !empty($_SESSION['logged_in']);
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gaming Store - Home</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/index.css" />
    <script src="./js/index.js" defer></script>
  </head>
  <body onload="loadContent()">
    <?php include "./pages/components/header.php" ?>
    <div class="featured-banner-container">
      <h1 class="featured-product-title">Featured Product</h1>
      <img
        src="./assets/product-images/product-1.webp"
        alt="Featured Product Banner"
        class="featured-product-banner"
      />
    </div>
  </body>
</html>
