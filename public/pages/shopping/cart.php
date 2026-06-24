<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Review your selected gaming peripherals and merchandise before checkout."
    />
    <title>Shopping Cart — Gaming Store</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/cart.css" />
  </head>
  <body>
    <!-- ========== HEADER (consistent with other pages) ========== -->
    <?php include "../components/header.php" ?>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="cart-page">
      <h1 class="cart-page-title">Shopping Cart</h1>

      <!-- Loading state -->
      <div class="cart-loading" id="cart-loading">
        <div class="spinner"></div>
        <p>Loading your cart...</p>
      </div>

      <!-- Cart content (populated by JS) -->
      <div class="cart-layout" id="cart-content" style="display: none">
        <!-- Cart items -->
        <div class="cart-items-section" id="cart-items">
          <!-- Items rendered by cart.js -->
        </div>
        <div class="summary-row total">
          <span>Estimated Total</span>
          <span id="cart-total">RM 0.00</span>
        </div>
        <a href="checkout.html" class="checkout-btn" id="checkout-btn">
          Proceed to Checkout
        </a>
      </aside>
    </div>

    <!-- Empty cart state -->
    <div class="empty-cart" id="empty-cart" style="display: none">
      <div class="empty-cart-icon">🛒</div>
      <p class="empty-cart-message">Your cart is empty</p>
      <p class="empty-cart-sub">
        Looks like you haven't added any items yet.
      </p>
      <a href="../../index.php" class="continue-shopping-btn">Continue Shopping</a>
    </div>
  </main>

  <script src="../../js/cart.js"></script>
</body>

</html>