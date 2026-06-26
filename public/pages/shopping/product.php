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
    <title><?= $product['name'] ?> — Gaming Store</title>
  </head>
  <body>
    <?php include "../components/header.php" ?>
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
        <h3 class="product-price"><?= $product['stock'] === 0 ? "SOLD OUT" : "RM" . $product['price'] ?></h3>
        <p class="product-desc"><?php echo $product['description']?></p>
        <div class="action-container">
          <button id="buy-now-btn" class="buy-btn" <?= $product['stock'] === 0 ? "disabled" : "" ?>>Buy Now</button>
          <button id="add-to-cart-btn" class="add-cart-btn" <?= $product['stock'] === 0 ? "disabled" : "" ?>>Add to Cart</button>
        </div>
        </div>
      </div>

      <!-- Add to Cart Modal -->
      <div id="cart-modal" class="modal-overlay">
        <div class="modal-content">
          <button id="close-modal" class="close-btn">&times;</button>
          <div class="modal-header">
            <span class="checkmark">&#10003;</span> ADDED TO CART
          </div>
          <div class="modal-body">
            <img src="../../<?php echo $product['image_path']?>" alt="Product Image" class="modal-product-img" />
            <div class="modal-product-info">
              <h4 class="modal-product-name"><?php echo $product['name']?></h4>
            </div>
          </div>
          <div class="modal-actions">
            <button id="view-cart-btn" class="modal-btn-secondary">View cart (<span id="modal-cart-count"></span>)</button>
            <button id="checkout-btn" class="modal-btn-primary">Checkout</button>
            <button id="continue-shopping-btn" class="modal-btn-tertiary">Continue shopping</button>
          </div>
        </div>
      </div>

      <script>
        window.userIsLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
      </script>
      <script src="../../js/product.js"></script>
    </body>
</html>
