<?php

require_once __DIR__ . '/../../../src/api/fetch_products.php';

$category = $_GET["category"] ?? "";
$sortStrategy = $_GET["sort"] ?? "default";
$products = fetchProducts($category, $sortStrategy);

?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/category.css" />
    <title>Gaming Store - <?= ucfirst($category) ?></title>
    <script src="../../js/category.js" defer></script>
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
        <select name="sort-by" id="sort-by" onchange="changeSort(this.value)">
          <option value="default" <?= ($sortStrategy === 'default') ? 'selected' : ''; ?>>Default</option>
          <option value="newly-added" <?= ($sortStrategy === 'newly-added') ? 'selected' : ''; ?>>Newly Added</option>
          <option value="alphabetical" <?= ($sortStrategy === 'alphabetical') ? 'selected' : ''; ?>>Alphabetical A-Z</option>
        </select>
      </div>
    </div>
    <div class="product-list-container">
      <?php foreach ($products as $product) : ?>
      <div class="item">
          <div class="item-img-container">
              <img
                  src="/gaming-store-webapp/public/<?= $product['image_path'] ?>"
                  alt=""
                  class="item-img"
              />
          </div>
          <div class="item-description">
            <a href="/gaming-store-webapp/public/pages/shopping/product.php?id=<?= $product['id'] ?>"><h3 class="item-name"><?= $product['name'] ?></h3></a>
            <p class="item-price">RM<?= $product['price'] ?></p>
          </div>
      </div>
      <?php endforeach; ?>
    </div>
  </body>
</html>
