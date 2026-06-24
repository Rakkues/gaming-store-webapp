<?php

require_once __DIR__ . '/../../../src/api/fetch_products.php';

$searchQuery = $_GET["name"];

$products = searchProducts($searchQuery);

?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/search.css" />
    <title>Product</title>
  </head>
  <body>
    <?php include "../components/header.php" ?>
    <h1>Showing results for "<?= $searchQuery ?>"</h1>
    <div class="search-list-container">
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
