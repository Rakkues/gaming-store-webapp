/**
 * checkout.js — Checkout Page Logic
 * CIT6224 Web Application Development | Member 3 (Cashier)
 *
 * Handles: loading order summary, form validation (client-side),
 *          shipping fee calculation, error display with scroll-to-top,
 *          and form submission to the server.
 */

// ============================================================
// Configuration
// ============================================================
const API_BASE = '../../api';

const SHIPPING_FEES = {
    ninjavan: 5.90,
    spx_express: 3.90,
    jnt_express: 6.90
};

// Store subtotal globally for shipping calculations
let currentSubtotal = 0;

// ============================================================
// DOM Ready
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    loadOrderSummary();
    setupShippingListeners();
    setupPaymentListeners();
    setupFormSubmit();
    setupInputClearErrors();
});

// ============================================================
// 1. Load Order Summary (Right Column)
// ============================================================
async function loadOrderSummary() {
    try {
        var response = await fetch(API_BASE + '/cart_get.php', {
            method: 'GET',
            credentials: 'include'
        });
        var data = await response.json();

        if (!data.success || data.items.length === 0) {
            alert('Your cart is empty. Redirecting to the shop...');
            window.location.href = '/index.html';
            return;
        }

        renderOrderSummary(data);
    } catch (error) {
        console.error('Error loading order summary:', error);
    }
}

function renderOrderSummary(data) {
    var container = document.getElementById('summary-items');
    container.innerHTML = '';

    for (var i = 0; i < data.items.length; i++) {
        var item = data.items[i];

        var el = document.createElement('div');
        el.className = 'summary-item';

        var html = '';
        html += '<div class="summary-item-img-wrap">';
        html += '  <img src="' + escapeHTML(item.image_path) + '" alt="' + escapeHTML(item.name) + '" class="summary-item-img" />';
        html += '  <span class="summary-item-qty-badge">' + item.quantity + '</span>';
        html += '</div>';
        html += '<div class="summary-item-info">';
        html += '  <p class="summary-item-name">' + escapeHTML(item.name) + '</p>';
        html += '</div>';
        html += '<span class="summary-item-price">RM ' + item.line_total + '</span>';

        el.innerHTML = html;
        container.appendChild(el);
    }

    // Store subtotal for shipping fee calculation
    currentSubtotal = parseFloat(data.subtotal.replace(/,/g, ''));

    document.getElementById('checkout-subtotal').textContent = 'RM ' + data.subtotal;
    updateShippingAndTotal();

    // Update shipping price labels if free shipping applies
    updateShippingPriceLabels();
}

// ============================================================
// 2. Shipping Method Listeners
// ============================================================
function setupShippingListeners() {
    var radios = document.querySelectorAll('input[name="shipping_method"]');
    for (var i = 0; i < radios.length; i++) {
        radios[i].addEventListener('change', function () {
            // Update selected styling
            var options = document.querySelectorAll('#group-shipping_method .radio-option');
            for (var j = 0; j < options.length; j++) {
                options[j].classList.remove('selected');
            }
            this.closest('.radio-option').classList.add('selected');

            // Clear shipping error
            clearRadioError('shipping_method');

            // Recalculate
            updateShippingAndTotal();
        });
    }
}

// ============================================================
// 3. Payment Method Listeners
// ============================================================
function setupPaymentListeners() {
    var radios = document.querySelectorAll('input[name="payment_method"]');
    for (var i = 0; i < radios.length; i++) {
        radios[i].addEventListener('change', function () {
            var options = document.querySelectorAll('#group-payment_method .radio-option');
            for (var j = 0; j < options.length; j++) {
                options[j].classList.remove('selected');
            }
            this.closest('.radio-option').classList.add('selected');

            // Clear payment error
            clearRadioError('payment_method');
        });
    }
}

// ============================================================
// 4. Shipping & Total Calculation
// ============================================================
function getSelectedShippingFee() {
    var selected = document.querySelector('input[name="shipping_method"]:checked');
    if (!selected) return null;

    // Free shipping for orders >= RM 150
    if (currentSubtotal >= 150) return 0;

    return SHIPPING_FEES[selected.value] || 0;
}

function updateShippingAndTotal() {
    var fee = getSelectedShippingFee();
    var shippingEl = document.getElementById('checkout-shipping');
    var totalEl = document.getElementById('checkout-total');

    if (fee === null) {
        // No shipping method selected yet
        shippingEl.textContent = '—';
        totalEl.textContent = 'RM ' + currentSubtotal.toFixed(2);
    } else if (fee === 0) {
        shippingEl.innerHTML = '<span class="free-label">FREE</span>';
        totalEl.textContent = 'RM ' + currentSubtotal.toFixed(2);
    } else {
        shippingEl.textContent = 'RM ' + fee.toFixed(2);
        totalEl.textContent = 'RM ' + (currentSubtotal + fee).toFixed(2);
    }
}

function updateShippingPriceLabels() {
    // If free shipping applies, update the radio labels
    if (currentSubtotal >= 150) {
        document.getElementById('price-spx').innerHTML = '<span class="free">FREE</span>';
        document.getElementById('price-ninjavan').innerHTML = '<span class="free">FREE</span>';
        document.getElementById('price-jnt').innerHTML = '<span class="free">FREE</span>';
    } else {
        document.getElementById('price-spx').textContent = 'RM 3.90';
        document.getElementById('price-ninjavan').textContent = 'RM 5.90';
        document.getElementById('price-jnt').textContent = 'RM 6.90';
    }
}

// ============================================================
// 5. Clear Errors on Input
// ============================================================
function setupInputClearErrors() {
    // Text inputs
    var inputs = document.querySelectorAll('.form-input, .form-select');
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('input', function () {
            var group = this.closest('.form-group');
            if (group) {
                group.classList.remove('has-error');
                var errorMsg = group.querySelector('.error-msg');
                if (errorMsg) {
                    errorMsg.style.display = 'none';
                    errorMsg.textContent = '';
                }
            }
        });

        // Also clear on change (for selects)
        inputs[i].addEventListener('change', function () {
            var group = this.closest('.form-group');
            if (group) {
                group.classList.remove('has-error');
                var errorMsg = group.querySelector('.error-msg');
                if (errorMsg) {
                    errorMsg.style.display = 'none';
                    errorMsg.textContent = '';
                }
            }
        });
    }
}

// ============================================================
// 6. Form Validation (Client-Side)
// ============================================================
function validateForm() {
    var errors = {};

    // Email
    var email = document.getElementById('email').value.trim();
    if (!email) {
        errors.email = 'Email address is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.email = 'Please enter a valid email address.';
    }

    // First name
    var firstName = document.getElementById('first_name').value.trim();
    if (!firstName) {
        errors.first_name = 'First name is required.';
    }

    // Last name
    var lastName = document.getElementById('last_name').value.trim();
    if (!lastName) {
        errors.last_name = 'Last name is required.';
    }

    // Address (mandatory - highlighted per user requirement)
    var address = document.getElementById('address').value.trim();
    if (!address) {
        errors.address = 'Address is required.';
    }

    // Postcode (mandatory - highlighted per user requirement)
    var postcode = document.getElementById('postcode').value.trim();
    if (!postcode) {
        errors.postcode = 'Postcode is required.';
    } else if (!/^\d{5}$/.test(postcode)) {
        errors.postcode = 'Postcode must be exactly 5 digits.';
    }

    // City (mandatory - highlighted per user requirement)
    var city = document.getElementById('city').value.trim();
    if (!city) {
        errors.city = 'City is required.';
    }

    // State (mandatory - highlighted per user requirement)
    var state = document.getElementById('state').value;
    if (!state) {
        errors.state = 'Please select a state.';
    }

    // Phone
    var phone = document.getElementById('phone').value.trim().replace(/[\s\-]/g, '');
    if (!phone) {
        errors.phone = 'Phone number is required.';
    } else if (!/^01\d{7,9}$/.test(phone)) {
        errors.phone = 'Please enter a valid Malaysian phone number (e.g., 0123456789).';
    }

    // Shipping method
    var shipping = document.querySelector('input[name="shipping_method"]:checked');
    if (!shipping) {
        errors.shipping_method = 'Please select a shipping method.';
    }

    // Payment method
    var payment = document.querySelector('input[name="payment_method"]:checked');
    if (!payment) {
        errors.payment_method = 'Please select a payment method.';
    }

    return errors;
}

// ============================================================
// 7. Show / Clear Errors
// ============================================================
function showErrors(errors) {
    // Clear all previous errors first
    clearAllErrors();

    // Show errors for text/select fields
    var fieldIds = ['email', 'first_name', 'last_name', 'address', 'postcode', 'city', 'state', 'phone'];
    for (var i = 0; i < fieldIds.length; i++) {
        var field = fieldIds[i];
        if (errors[field]) {
            showFieldError(field, errors[field]);
        }
    }

    // Show errors for radio groups
    if (errors.shipping_method) {
        showRadioError('shipping_method', errors.shipping_method);
    }
    if (errors.payment_method) {
        showRadioError('payment_method', errors.payment_method);
    }
}

function showFieldError(fieldId, message) {
    var group = document.getElementById('group-' + fieldId);
    if (group) {
        group.classList.add('has-error');
        var errorMsg = document.getElementById('error-' + fieldId);
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }
}

function showRadioError(groupId, message) {
    var group = document.getElementById('group-' + groupId);
    if (group) {
        group.classList.add('has-error');
        var errorMsg = document.getElementById('error-' + groupId);
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }
}

function clearRadioError(groupId) {
    var group = document.getElementById('group-' + groupId);
    if (group) {
        group.classList.remove('has-error');
        var errorMsg = document.getElementById('error-' + groupId);
        if (errorMsg) {
            errorMsg.style.display = 'none';
            errorMsg.textContent = '';
        }
    }
}

function clearAllErrors() {
    // Clear text field errors
    var groups = document.querySelectorAll('.form-group');
    for (var i = 0; i < groups.length; i++) {
        groups[i].classList.remove('has-error');
    }
    var msgs = document.querySelectorAll('.error-msg');
    for (var i = 0; i < msgs.length; i++) {
        msgs[i].style.display = 'none';
        msgs[i].textContent = '';
    }

    // Clear radio group errors
    var radioGroups = document.querySelectorAll('.radio-group');
    for (var i = 0; i < radioGroups.length; i++) {
        radioGroups[i].classList.remove('has-error');
    }
    var radioMsgs = document.querySelectorAll('.radio-error-msg');
    for (var i = 0; i < radioMsgs.length; i++) {
        radioMsgs[i].style.display = 'none';
        radioMsgs[i].textContent = '';
    }
}

// ============================================================
// 8. Form Submission
// ============================================================
function setupFormSubmit() {
    var form = document.getElementById('checkout-form');
    form.addEventListener('submit', handleSubmit);
}

async function handleSubmit(e) {
    e.preventDefault();

    // ---- Client-side validation ----
    var errors = validateForm();

    if (Object.keys(errors).length > 0) {
        showErrors(errors);

        // Scroll to top so user sees the red-highlighted fields
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    // ---- Prepare form data ----
    var formData = new FormData();
    formData.append('email', document.getElementById('email').value.trim());
    formData.append('first_name', document.getElementById('first_name').value.trim());
    formData.append('last_name', document.getElementById('last_name').value.trim());
    formData.append('address', document.getElementById('address').value.trim());
    formData.append('address2', document.getElementById('address2').value.trim());
    formData.append('postcode', document.getElementById('postcode').value.trim());
    formData.append('city', document.getElementById('city').value.trim());
    formData.append('state', document.getElementById('state').value);
    formData.append('phone', document.getElementById('phone').value.trim());
    formData.append('shipping_method', document.querySelector('input[name="shipping_method"]:checked').value);
    formData.append('payment_method', document.querySelector('input[name="payment_method"]:checked').value);

    // ---- Disable button & show loading ----
    var payBtn = document.getElementById('pay-btn');
    var loading = document.getElementById('checkout-loading');

    payBtn.disabled = true;
    payBtn.textContent = 'Processing...';
    loading.classList.add('active');

    try {
        var response = await fetch(API_BASE + '/checkout_process.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });
        var data = await response.json();

        if (data.success) {
            // Redirect to receipt page
            window.location.href = data.redirect || '/pages/shopping/receipt.html';
        } else if (data.errors) {
            // Server-side validation errors
            loading.classList.remove('active');
            showErrors(data.errors);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            payBtn.disabled = false;
            payBtn.textContent = 'Pay Now';
        } else {
            // General error
            loading.classList.remove('active');
            alert(data.message || 'An error occurred. Please try again.');
            payBtn.disabled = false;
            payBtn.textContent = 'Pay Now';
        }
    } catch (error) {
        console.error('Checkout error:', error);
        loading.classList.remove('active');
        alert('A network error occurred. Please check your connection and try again.');
        payBtn.disabled = false;
        payBtn.textContent = 'Pay Now';
    }
}

// ============================================================
// Utility: Escape HTML to prevent XSS
// ============================================================
function escapeHTML(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
