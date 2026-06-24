/**
 * receipt.js — Receipt / Thank You Page Logic
 * CIT6224 Web Application Development | Member 3 (Cashier)
 *
 * Handles: loading order data from the server and rendering
 *          the full receipt with Transaction ID, Order Number,
 *          Tracking ID, order items, delivery info, and totals.
 */

// ============================================================
// Configuration
// ============================================================
const API_BASE = '/api';

// ============================================================
// DOM Ready
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    loadReceipt();
});

// ============================================================
// Load Receipt Data
// ============================================================
async function loadReceipt() {
    var loadingEl = document.getElementById('receipt-loading');
    var cardEl    = document.getElementById('receipt-card');

    loadingEl.style.display = 'block';
    cardEl.style.display    = 'none';

    try {
        var response = await fetch(API_BASE + '/receipt_get.php', {
            method: 'GET',
            credentials: 'include'
        });
        var data = await response.json();

        loadingEl.style.display = 'none';

        if (data.success) {
            renderReceipt(data);
            cardEl.style.display = 'block';
        } else {
            // No order found — show error message
            cardEl.innerHTML = '<div class="receipt-error">' +
                '<p>' + escapeHTML(data.message || 'No order found. Please complete a purchase first.') + '</p>' +
                '<a href="../../index.html" class="btn-primary">Go to Home</a>' +
                '</div>';
            cardEl.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading receipt:', error);
        loadingEl.style.display = 'none';
        cardEl.innerHTML = '<div class="receipt-error">' +
            '<p>Unable to load your receipt. Please try again later.</p>' +
            '<a href="../../index.html" class="btn-primary">Go to Home</a>' +
            '</div>';
        cardEl.style.display = 'block';
    }
}

// ============================================================
// Render Receipt
// ============================================================
function renderReceipt(data) {
    var order = data.order;
    var items = data.items;

    // ---- Key Info ----
    document.getElementById('transaction-id').textContent = order.transaction_id || '—';
    document.getElementById('order-number').textContent   = order.order_number   || '—';
    document.getElementById('tracking-id').textContent    = order.tracking_id    || '—';

    // ---- Order Items ----
    var itemsContainer = document.getElementById('receipt-items-list');
    itemsContainer.innerHTML = '';

    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var el = document.createElement('div');
        el.className = 'receipt-item';

        var imgPath = item.image_path || 'assets/product-images/product-1.webp';

        el.innerHTML =
            '<img src="../../' + escapeHTML(imgPath) + '" alt="' + escapeHTML(item.product_name) + '" class="receipt-item-img" />' +
            '<span class="receipt-item-name">' + escapeHTML(item.product_name) + '</span>' +
            '<span class="receipt-item-qty">×' + item.quantity + '</span>' +
            '<span class="receipt-item-price">RM ' + item.line_total + '</span>';

        itemsContainer.appendChild(el);
    }

    // ---- Delivery Info ----
    document.getElementById('delivery-name').textContent    = order.first_name + ' ' + order.last_name;
    document.getElementById('delivery-address').textContent = order.address + (order.address2 ? ', ' + order.address2 : '');
    document.getElementById('delivery-city').textContent    = order.postcode + ' ' + order.city + ', ' + order.state;
    document.getElementById('delivery-country').textContent = order.country;
    document.getElementById('delivery-phone').textContent   = order.phone;

    // ---- Shipping & Payment ----
    document.getElementById('shipping-method-name').textContent = order.shipping_method_name || order.shipping_method;
    document.getElementById('payment-method-name').textContent  = order.payment_method_name  || order.payment_method;

    // Order status
    var statusEl = document.getElementById('order-status');
    if (statusEl) {
        statusEl.textContent = capitalizeFirst(order.status);
    }

    // ---- Totals ----
    document.getElementById('receipt-subtotal').textContent = 'RM ' + order.subtotal;

    var shippingEl = document.getElementById('receipt-shipping');
    if (order.shipping_fee === '0.00') {
        shippingEl.innerHTML = '<span class="free-label">FREE</span>';
    } else {
        shippingEl.textContent = 'RM ' + order.shipping_fee;
    }

    document.getElementById('receipt-total').textContent = 'RM ' + order.total;
}

// ============================================================
// Utility Functions
// ============================================================
function escapeHTML(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}
