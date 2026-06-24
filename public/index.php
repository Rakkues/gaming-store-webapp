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
    <header>
      <div class="header-top">
        <div class="store-logo-container">
          <a href="https://localhost/gaming-store-webapp/public/">
            <img src="./assets/store-logo.png" alt="store logo" />
          </a>
        </div>
        <input type="text" class="search-bar" placeholder="Search" />
        <div class="login-cart-container">
          <?php if ($isLoggedIn): ?>
            <button onclick="window.location.href = './pages/shopping/cart.html'">
              Cart
            </button>
            <button onclick="window.location.href = './pages/auth/logout.php'">
              Logout
            </button>
          <?php else: ?>
            <button onclick="window.location.href = './pages/auth/login.php'">
              Login
            </button>
          <?php endif; ?>
        </div>
      </div>
      <div class="header-bottom">
        <nav class="navbar">
          <ul>
            <li>
              <a href="https://localhost/gaming-store-webapp/public/">Home</a>
            </li>
            <li>
              <a href="./pages/shopping/category.php?category=mouse">Mouse</a>
            </li>
            <li>
              <a href="./pages/shopping/category.php?category=keyboard"
                >Keyboard</a
              >
            </li>
            <li>
              <a href="./pages/shopping/category.php?category=audio">Audio</a>
            </li>
            <li>
              <a
                href="/public/pages/shopping/category.php?category=collectibles"
                >Collectibles</a
              >
            </li>
          </ul>
        </nav>
      </div>
    </header>
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
