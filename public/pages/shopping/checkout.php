<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Complete your purchase — enter shipping details and payment method."
    />
    <title>Checkout — Gaming Store</title>
    <link rel="stylesheet" href="../../css/checkout.css" />
  </head>
  <body>
    <!-- ========== LOADING OVERLAY ========== -->
    <div class="checkout-loading" id="checkout-loading">
      <div class="spinner"></div>
      <p>Processing your order...</p>
    </div>

    <!-- ========== MAIN CHECKOUT LAYOUT ========== -->
    <main class="checkout-page">
      <div class="checkout-layout">
        <!-- ======== LEFT COLUMN: FORM ======== -->
        <div class="checkout-form-container">
          <!-- Store Logo -->
          <div class="checkout-logo">
            <a href="/gaming-store-webapp/public/">
              <img src="../../assets/store-logo.png" alt="Gaming Store Logo" />
            </a>
          </div>

          <form id="checkout-form" novalidate autocomplete="on">
            <!-- ---- Contact ---- -->
            <section class="form-section">
              <h2 class="form-section-title">Contact</h2>
              <div class="form-group" id="group-email">
                <label class="form-label" for="email"
                  >Email <span class="required-marker">*</span></label
                >
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-input"
                  placeholder="your@email.com"
                  required
                  autocomplete="email"
                />
                <span class="error-msg" id="error-email"></span>
              </div>
            </section>

            <!-- ---- Delivery ---- -->
            <section class="form-section">
              <h2 class="form-section-title">Delivery</h2>

              <!-- Country/Region -->
              <div class="form-group" id="group-country">
                <label class="form-label" for="country">Country/Region</label>
                <select
                  id="country"
                  name="country"
                  class="form-select"
                  disabled
                >
                  <option value="Malaysia" selected>Malaysia</option>
                </select>
              </div>

              <!-- First / Last Name -->
              <div class="form-row">
                <div class="form-group" id="group-first_name">
                  <label class="form-label" for="first_name"
                    >First Name <span class="required-marker">*</span></label
                  >
                  <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    class="form-input"
                    placeholder="First Name"
                    required
                    autocomplete="given-name"
                  />
                  <span class="error-msg" id="error-first_name"></span>
                </div>
                <div class="form-group" id="group-last_name">
                  <label class="form-label" for="last_name"
                    >Last Name <span class="required-marker">*</span></label
                  >
                  <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    class="form-input"
                    placeholder="Last Name"
                    required
                    autocomplete="family-name"
                  />
                  <span class="error-msg" id="error-last_name"></span>
                </div>
              </div>

              <!-- Address -->
              <div class="form-group" id="group-address">
                <label class="form-label" for="address"
                  >Address <span class="required-marker">*</span></label
                >
                <input
                  type="text"
                  id="address"
                  name="address"
                  class="form-input"
                  placeholder="Street address"
                  required
                  autocomplete="address-line1"
                />
                <span class="error-msg" id="error-address"></span>
              </div>

              <!-- Apartment (optional) -->
              <div class="form-group" id="group-address2">
                <label class="form-label" for="address2"
                  >Apartment, Suite (Optional)</label
                >
                <input
                  type="text"
                  id="address2"
                  name="address2"
                  class="form-input"
                  placeholder="Apartment, suite, unit, etc."
                  autocomplete="address-line2"
                />
              </div>

              <!-- Postcode / City / State -->
              <div class="form-row three-col">
                <div class="form-group" id="group-postcode">
                  <label class="form-label" for="postcode"
                    >Postcode <span class="required-marker">*</span></label
                  >
                  <input
                    type="text"
                    id="postcode"
                    name="postcode"
                    class="form-input"
                    placeholder="e.g. 63100"
                    maxlength="5"
                    required
                    autocomplete="postal-code"
                  />
                  <span class="error-msg" id="error-postcode"></span>
                </div>
                <div class="form-group" id="group-city">
                  <label class="form-label" for="city"
                    >City <span class="required-marker">*</span></label
                  >
                  <input
                    type="text"
                    id="city"
                    name="city"
                    class="form-input"
                    placeholder="City"
                    required
                    autocomplete="address-level2"
                  />
                  <span class="error-msg" id="error-city"></span>
                </div>
                <div class="form-group" id="group-state">
                  <label class="form-label" for="state"
                    >State <span class="required-marker">*</span></label
                  >
                  <select id="state" name="state" class="form-select" required>
                    <option value="">Select State</option>
                    <option value="Johor">Johor</option>
                    <option value="Kedah">Kedah</option>
                    <option value="Kelantan">Kelantan</option>
                    <option value="Melaka">Melaka</option>
                    <option value="Negeri Sembilan">Negeri Sembilan</option>
                    <option value="Pahang">Pahang</option>
                    <option value="Perak">Perak</option>
                    <option value="Perlis">Perlis</option>
                    <option value="Pulau Pinang">Pulau Pinang</option>
                    <option value="Sabah">Sabah</option>
                    <option value="Sarawak">Sarawak</option>
                    <option value="Selangor">Selangor</option>
                    <option value="Terengganu">Terengganu</option>
                    <option value="W.P. Kuala Lumpur">W.P. Kuala Lumpur</option>
                    <option value="W.P. Labuan">W.P. Labuan</option>
                    <option value="W.P. Putrajaya">W.P. Putrajaya</option>
                  </select>
                  <span class="error-msg" id="error-state"></span>
                </div>
              </div>

              <!-- Phone -->
              <div class="form-group" id="group-phone">
                <label class="form-label" for="phone"
                  >Phone <span class="required-marker">*</span></label
                >
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  class="form-input"
                  placeholder="e.g. 0123456789"
                  required
                  autocomplete="tel"
                />
                <span class="error-msg" id="error-phone"></span>
              </div>
            </section>

            <!-- ---- Shipping Method ---- -->
            <section class="form-section">
              <h2 class="form-section-title">Shipping Method</h2>
              <div class="radio-group" id="group-shipping_method">
                <label class="radio-option" id="opt-spx">
                  <input
                    type="radio"
                    name="shipping_method"
                    value="spx_express"
                  />
                  <span class="option-label">SPX Express</span>
                  <span class="option-price" id="price-spx">RM 3.90</span>
                </label>
                <label class="radio-option" id="opt-ninjavan">
                  <input type="radio" name="shipping_method" value="ninjavan" />
                  <span class="option-label">NinjaVan</span>
                  <span class="option-price" id="price-ninjavan">RM 5.90</span>
                </label>
                <label class="radio-option" id="opt-jnt">
                  <input
                    type="radio"
                    name="shipping_method"
                    value="jnt_express"
                  />
                  <span class="option-label">J&T Express</span>
                  <span class="option-price" id="price-jnt">RM 6.90</span>
                </label>
                <span class="radio-error-msg" id="error-shipping_method"></span>
              </div>
            </section>

            <!-- ---- Payment ---- -->
            <section class="form-section">
              <h2 class="form-section-title">Payment</h2>
              <div class="radio-group" id="group-payment_method">
                <label class="radio-option" id="opt-credit-card">
                  <input
                    type="radio"
                    name="payment_method"
                    value="credit_card"
                  />
                  <span class="option-label">Credit Card</span>
                </label>
                <label class="radio-option" id="opt-spaylater">
                  <input type="radio" name="payment_method" value="spaylater" />
                  <span class="option-label">SPayLater</span>
                </label>
                <label class="radio-option" id="opt-atome">
                  <input type="radio" name="payment_method" value="atome" />
                  <span class="option-label">Atome</span>
                </label>
                <span class="radio-error-msg" id="error-payment_method"></span>
              </div>
            </section>

            <!-- ---- Submit ---- -->
            <button type="submit" class="pay-btn" id="pay-btn">Pay Now</button>
          </form>
        </div>

        <!-- ======== RIGHT COLUMN: ORDER SUMMARY ======== -->
        <aside class="checkout-summary">
          <h2 class="summary-title">Order Summary</h2>

          <!-- Items (populated by JS) -->
          <div id="summary-items">
            <!-- Rendered by checkout.js -->
          </div>

          <!-- Totals -->
          <div class="summary-totals">
            <div class="summary-row">
              <span>Subtotal</span>
              <span id="checkout-subtotal">RM 0.00</span>
            </div>
            <div class="summary-row">
              <span>Shipping</span>
              <span id="checkout-shipping">—</span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span id="checkout-total">RM 0.00</span>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <script src="../../js/checkout.js"></script>
  </body>
</html>
