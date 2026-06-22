<?php

header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../../../src/config/database.php';

$product = null;
$error = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        // Query restaurant details
        $conn = getDBConnection();
        $rest_stmt = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1;");
        $rest_stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $rest_stmt->execute();
        $product = $rest_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $error = "Product not found.";
        }
    } catch (PDOException $e) {
        $error = "Database query failed: " . $e->getMessage();
    }
} else {
    $error = "No product ID provided.";
}

?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/product.css" />
    <title>Product</title>
  </head>
  <body>
    <header>
      <div class="header-top">
        <div class="store-logo-container">
          <a href="https://localhost/gaming-store-webapp/public/">
            <img src="../../assets/store-logo.png" alt="store logo" />
          </a>
        </div>
        <input type="text" class="search-bar" placeholder="Search" />
        <div class="login-cart-container">
          <button onclick="window.location.href = '/pages/auth/login.html'">
            Login
          </button>
          <button onclick="window.location.href = '/pages/shopping/cart.html'">
            Cart
          </button>
        </div>
      </div>
      <div class="header-bottom">
        <nav class="navbar">
          <ul>
            <li><a href="https://localhost/gaming-store-webapp/public/">Home</a></li>
            <li><a href="">Mouse</a></li>
            <li><a href="">Keyboard</a></li>
            <li><a href="">Audio</a></li>
            <li><a href="">Collectibles</a></li>
          </ul>
        </nav>
      </div>
    </header>
    <div class="content-container">
      <div class="photo-gallery-container">
        <div class="display-photo-container">
          <img
            src="../../<?php echo $product['image_path']?>"
            alt="display - photo"
            class="main - photo"
          />
        </div>
      </div>
      <div class="product-desc-container">
        <h1 class="product-name"><?php echo $product['name']?></h1>
        <h3 class="product-price">RM<?php echo $product['price']?></h3>
        <p class="product-desc"><?php echo $product['description']?></p>
        <div class="action-container">
          <button class="buy-btn">Buy Now</button>
          <button class="add-cart-btn">Add to Cart</button>
        </div>
        </div>
      </div>
    </body>
</html>
