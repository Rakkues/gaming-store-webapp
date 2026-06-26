<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminName = $_SESSION['username'] ?? 'Admin';
?>
<header>
  <div class="header-top">
    <div class="store-logo-container">
      <a href="/gaming-store-webapp/public/pages/admin/admin.php">
        <img src="/gaming-store-webapp/public/assets/store-logo.png" alt="store logo" />
      </a>
    </div>
    <h1 class="admin-header-title">Gaming Store Admin</h1>
    <div class="login-cart-container">
      <button onclick="window.location.href = '/gaming-store-webapp/public/pages/auth/logout.php'">
        Logout
      </button>
    </div>
  </div>
  <div class="header-bottom">
    <nav class="navbar">
      <ul>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/admin/admin.php">Dashboard</a>
        </li>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/admin/inventory.php">Inventory</a>
        </li>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/admin/order_history.php">Order History</a>
        </li>
        <li class="header-link">
          <a href="/gaming-store-webapp/public/pages/admin/members.php">Members</a>
        </li>
      </ul>
    </nav>
  </div>
</header>
