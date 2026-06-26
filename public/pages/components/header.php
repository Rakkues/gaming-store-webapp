<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['logged_in']);
?>
<header>
  <div class="header-top">
    <div class="store-logo-container">
      <a href="https://localhost/gaming-store-webapp/public/">
        <img src="/gaming-store-webapp/public/assets/store-logo.png" alt="store logo" />
      </a>
    </div>
    <form action="/gaming-store-webapp/public/pages/shopping/search.php" method="get">
      <input type="text" name="name" class="search-bar" placeholder="Search for a product..." />
      <button type="submit" class="search-btn">Search</button>
    </form>
    <div class="login-cart-container">
      <?php if ($isLoggedIn) : ?>
        <button onclick="window.location.href = '/gaming-store-webapp/public/pages/shopping/cart.php'">
          Cart
        </button>
        <button onclick="window.location.href = '/gaming-store-webapp/public/pages/auth/logout.php'">
          Logout
        </button>
      <?php else : ?>
        <button onclick="window.location.href = '/gaming-store-webapp/public/pages/auth/login.php'">
          Login
        </button>
      <?php endif; ?>
    </div>
  </div>
  <div class="header-bottom">
    <nav class="navbar">
      <ul>
        <li class="header-link">
          <a href="https://localhost/gaming-store-webapp/public/">Home</a>
        </li>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/shopping/category.php?category=mouse">Mouse</a>
        </li>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/shopping/category.php?category=keyboard"
            >Keyboard</a
          >
        </li>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/shopping/category.php?category=audio">Audio</a>
        </li>
        <li class="header-link">
          <a
            href="/gaming-store-webapp/public/pages/shopping/category.php?category=collectibles"
            >Collectibles</a
          >
        </li>
      </ul>
    </nav>
  </div>
</header>
