document.addEventListener("DOMContentLoaded", () => {
  const buyNowBtn = document.getElementById("buy-now-btn");
  const addToCartBtn = document.getElementById("add-to-cart-btn");
  const cartModal = document.getElementById("cart-modal");
  const closeModalBtn = document.getElementById("close-modal");
  const viewCartBtn = document.getElementById("view-cart-btn");
  const checkoutBtn = document.getElementById("checkout-btn");
  const continueShoppingBtn = document.getElementById("continue-shopping-btn");
  const modalCartCount = document.getElementById("modal-cart-count");

  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get("id");

  const addToCart = async (redirectUrl = null, showModal = false) => {
    if (!window.userIsLoggedIn) {
      alert("You must be logged in to add items to your cart.");
      window.location.href = "/gaming-store-webapp/public/pages/auth/login.php";
      return;
    }

    if (!productId) {
      alert("Invalid product.");
      return;
    }

    try {
      const formData = new FormData();
      formData.append("product_id", productId);
      formData.append("quantity", 1);

      const response = await fetch("../../api/cart_add.php", {
        method: "POST",
        body: formData,
        credentials: "include",
      });

      const result = await response.json();

      if (result.success) {
        if (redirectUrl) {
          window.location.href = redirectUrl;
        } else if (showModal) {
          if (modalCartCount) {
            modalCartCount.textContent = result.cart_count;
          }
          if (cartModal) {
            cartModal.style.display = "flex";
          }
        }
      } else {
        alert(result.message || "Failed to add to cart.");
      }
    } catch (error) {
      console.error("Error adding to cart:", error);
      alert("An error occurred.");
    }
  };

  if (buyNowBtn) {
    buyNowBtn.addEventListener("click", () => {
      // Add to cart and redirect to checkout
      addToCart("/gaming-store-webapp/public/pages/shopping/checkout.html");
    });
  }

  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", () => {
      // Add to cart and show modal
      addToCart(null, true);
    });
  }

  // Modal actions
  const closeModal = () => {
    if (cartModal) {
      cartModal.style.display = "none";
    }
  };

  if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
  if (continueShoppingBtn)
    continueShoppingBtn.addEventListener("click", closeModal);

  if (viewCartBtn) {
    viewCartBtn.addEventListener("click", () => {
      window.location.href =
        "/gaming-store-webapp/public/pages/shopping/cart.php";
    });
  }

  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", () => {
      window.location.href =
        "/gaming-store-webapp/public/pages/shopping/checkout.html";
    });
  }

  // Close modal when clicking outside
  window.addEventListener("click", (e) => {
    if (e.target === cartModal) {
      closeModal();
    }
  });
});
