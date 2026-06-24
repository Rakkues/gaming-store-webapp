/**
 * cart.js — Shopping Cart Page Logic
 * CIT6224 Web Application Development | Member 3 (Cashier)
 *
 * Handles: loading cart, rendering items, quantity updates, item removal,
 *          and order summary calculations.
 */

// ============================================================
// Configuration
// ============================================================
const API_BASE = '/api';

// ============================================================
// DOM Ready
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    loadCart();
});

// ============================================================
// Load Cart from Server
// ============================================================
async function loadCart() {
    const loadingEl  = document.getElementById('cart-loading');
    const contentEl  = document.getElementById('cart-content');
    const emptyEl    = document.getElementById('empty-cart');

    // Show loading
    loadingEl.style.display = 'block';
    contentEl.style.display = 'none';
    emptyEl.style.display   = 'none';

    try {
        const response = await fetch(API_BASE + '/cart_get.php', {
            method: 'GET',
            credentials: 'include'  // send session cookie
        });
        const data = await response.json();

        loadingEl.style.display = 'none';

        if (data.success && data.items.length > 0) {
            renderCart(data);
            contentEl.style.display = 'flex';
        } else {
            emptyEl.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        loadingEl.style.display = 'none';
        emptyEl.style.display   = 'block';
    }
}

// ============================================================
// Render Cart Items
// ============================================================
function renderCart(data) {
    var container = document.getElementById('cart-items');
    container.innerHTML = '';

    for (var i = 0; i < data.items.length; i++) {
        var item = data.items[i];
        var atMaxStock = item.quantity >= item.stock;

        var itemDiv = document.createElement('div');
        itemDiv.className = 'cart-item';
        itemDiv.id = 'cart-item-' + item.product_id;

        // Build inner HTML
        var html = '';
        html += '<img src="../../' + escapeHTML(item.image_path) + '" alt="' + escapeHTML(item.name) + '" class="cart-item-image" />';
        html += '<div class="cart-item-details">';
        html += '  <p class="cart-item-name">' + escapeHTML(item.name) + '</p>';
        html += '  <p class="cart-item-price">RM ' + item.price + '</p>';
        if (atMaxStock) {
            html += '  <p class="stock-warning">Max stock reached (' + item.stock + ' available)</p>';
        }
        html += '</div>';
        html += '<div class="quantity-controls">';
        html += '  <button class="qty-btn" onclick="updateQuantity(' + item.product_id + ', \'decrement\')" title="Decrease quantity">−</button>';
        html += '  <span class="qty-value">' + item.quantity + '</span>';
        html += '  <button class="qty-btn" onclick="updateQuantity(' + item.product_id + ', \'increment\')"' + (atMaxStock ? ' disabled' : '') + ' title="Increase quantity">+</button>';
        html += '</div>';
        html += '<span class="cart-item-total">RM ' + item.line_total + '</span>';
        html += '<button class="remove-btn" onclick="updateQuantity(' + item.product_id + ', \'remove\')" title="Remove item">✕ Remove</button>';

        itemDiv.innerHTML = html;
        container.appendChild(itemDiv);
    }

    // Update summary
    updateSummary(data.subtotal, data.shipping, data.total);
}

// ============================================================
// Update Order Summary
// ============================================================
function updateSummary(subtotal, shipping, total) {
    document.getElementById('cart-subtotal').textContent = 'RM ' + subtotal;

    var shippingEl = document.getElementById('cart-shipping');
    if (shipping === '0.00') {
        shippingEl.textContent = 'FREE';
        shippingEl.className = 'free-shipping';
    } else {
        shippingEl.textContent = 'Calculated at checkout';
        shippingEl.className = '';
    }

    document.getElementById('cart-total').textContent = 'RM ' + subtotal;
}

// ============================================================
// Update Quantity (increment / decrement / remove)
// ============================================================
async function updateQuantity(productId, action) {
    try {
        var formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', action);

        var response = await fetch(API_BASE + '/cart_update.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });
        var data = await response.json();

        if (data.success) {
            // Reload the full cart to get fresh data from DB
            loadCart();
        } else {
            showNotification(data.message || 'Error updating cart.', 'error');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showNotification('Network error. Please try again.', 'error');
    }
}

// ============================================================
// Notification Toast
// ============================================================
function showNotification(message, type) {
    // Remove existing notification
    var existing = document.querySelector('.cart-notification');
    if (existing) existing.remove();

    var notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.style.cssText = 'position:fixed;top:1rem;right:1rem;padding:0.75rem 1.25rem;border-radius:6px;font-size:0.9rem;font-weight:500;z-index:9999;animation:slideIn 0.3s ease;';

    if (type === 'error') {
        notification.style.background = '#dc3545';
        notification.style.color = '#fff';
    } else {
        notification.style.background = '#28a745';
        notification.style.color = '#fff';
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(function () {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease';
        setTimeout(function () { notification.remove(); }, 300);
    }, 3000);
}

// ============================================================
// Utility: Escape HTML to prevent XSS
// ============================================================
function escapeHTML(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
