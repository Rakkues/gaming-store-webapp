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
    <title><?= ucfirst($category) ?> — Gaming Store</title>
    <script src="../../js/category.js" defer></script>
  </head>
  <body>
    <?php include "../components/header.php" ?>
    <div class="category-header-container">
      <h1><?= ucfirst($category) ?></h1>
      <div class="sort-choice-container">
        <p>Sort by:</p>
        <select name="sort-by" id="sort-by" onchange="changeSort(this.value)">
          <option value="default" <?= ($sortStrategy === 'default') ? 'selected' : ''; ?>>Default</option>
          <option value="newly-added" <?= ($sortStrategy === 'newly-added') ? 'selected' : ''; ?>>Newly Added</option>
          <option value="alphabetical-az" <?= ($sortStrategy === 'alphabetical-az') ? 'selected' : ''; ?>>Alphabetical A-Z</option>
          <option value="alphabetical-za" <?= ($sortStrategy === 'alphabetical-za') ? 'selected' : ''; ?>>Alphabetical Z-A</option>
          <option value="price-lowhigh" <?= ($sortStrategy === 'price-lowhigh') ? 'selected' : ''; ?>>Price: Low to High</option>
          <option value="price-highlow" <?= ($sortStrategy === 'price-highlow') ? 'selected' : ''; ?>>Price: High to Low</option>
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
