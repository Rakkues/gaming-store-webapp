<?php

require_once __DIR__ . '/../../../src/api/fetch_products.php';

$category = $_GET["category"] ?? "";
$products = fetchProducts($category);

?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/category.css" />
    <title>Category</title>
  </head>
  <body>
    <header>
      <div class="header-top">
        <div class="store-logo-container">
          <a href="#home">
            <img src="../../assets/store-logo.png" alt="store logo" />
          </a>
        </div>
        <input type="text" class="search-bar" placeholder="Search" />
        <div class="login-cart-container">
          <button
            onclick="window.location.href = '../../pages/auth/login.html'"
          >
            Login
          </button>
          <button
            onclick="window.location.href = '../../pages/shopping/cart.html'"
          >
            Cart
          </button>
        </div>
      </div>
      <div class="header-bottom">
        <nav class="navbar">
          <ul>
            <li><a href="../../">Home</a></li>
            <li><a href="category.php?category=mouse">Mouse</a></li>
            <li><a href="category.php?category=keyboard">Keyboard</a></li>
            <li><a href="category.php?category=audio">Audio</a></li>
            <li><a href="category.php?category=collectibles">Collectibles</a></li>
          </ul>
        </nav>
      </div>
    </header>
    <div class="category-header-container">
      <h1><?= ucfirst($category) ?></h1>
      <div class="sort-choice-container">
        <p>Sort by:</p>
        <select name="sort-by" id="sort-by">
          <option value="newly-added">Newly Added</option>
        </select>
      </div>
    </div>
    <div class="product-list-container">
      <?php foreach ($products as $product) : ?>
        <div class="product-container">
          <div class="product-img-container">
            <img
              src="../../<?= $product['image_path'] ?>"
              alt="product image"
              class="product-img"
            />
          </div>
          <h3 class="product-name"><?= $product['name'] ?></h3>
          <p class="product-price">RM<?= $product['price'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </body>
</html>
