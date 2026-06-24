<header>
  <div class="header-top">
    <div class="store-logo-container">
      <a href="https://localhost/gaming-store-webapp/public/">
        <img src="/gaming-store-webapp/public/assets/store-logo.png" alt="store logo" />
      </a>
    </div>
    <input type="text" class="search-bar" placeholder="Search" />
    <div class="login-cart-container">
      <?php if ($isLoggedIn) : ?>
        <button onclick="window.location.href = './pages/shopping/cart.html'">
          Cart
        </button>
        <button onclick="window.location.href = './pages/auth/logout.php'">
          Logout
        </button>
      <?php else : ?>
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
          <a href="/gaming-store-webapp/public/pages/shopping/category.php?category=mouse">Mouse</a>
        </li>
        <li>
          <a href="/gaming-store-webapp/public/pages/shopping/category.php?category=keyboard"
            >Keyboard</a
          >
        </li>
        <li>
          <a href="/gaming-store-webapp/public/pages/shopping/category.php?category=audio">Audio</a>
        </li>
        <li>
          <a
            href="/gaming-store-webapp/public/pages/shopping/category.php?category=collectibles"
            >Collectibles</a
          >
        </li>
      </ul>
    </nav>
  </div>
</header>
