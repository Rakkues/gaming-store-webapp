<?php
/**
 * cart_get.php — Return current cart contents with live DB prices/stock
 * Method: GET
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
requireSession();

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo json_encode([
        'success'    => true,
        'items'      => [],
        'subtotal'   => '0.00',
        'shipping'   => '10.00',
        'total'      => '10.00',
        'cart_count' => 0,
    ]);
    exit;
}

$pdo = getDBConnection();
$ids = implode(',', array_map('intval', array_keys($cart)));

// Fetch live product data (price may have changed since add-to-cart)
$stmt = $pdo->query("SELECT id, name, price, stock, image_path FROM products WHERE id IN ($ids)");
$products = [];
foreach ($stmt->fetchAll() as $row) {
    $products[$row['id']] = $row;
}

$items    = [];
$subtotal = 0.0;

foreach ($cart as $pid => $entry) {
    $product = $products[$pid] ?? null;
    if (!$product) continue; // Product deleted from store

    // Clamp quantity to available stock
    $qty = min($entry['quantity'], (int) $product['stock']);
    if ($qty !== $entry['quantity']) {
        $_SESSION['cart'][$pid]['quantity'] = $qty;
    }

    $line_total = round($product['price'] * $qty, 2);
    $subtotal  += $line_total;

    $items[] = [
        'product_id'  => (int) $pid,
        'name'        => $product['name'],
        'price'       => number_format((float) $product['price'], 2),
        'quantity'    => $qty,
        'stock'       => (int) $product['stock'],
        'line_total'  => number_format($line_total, 2),
        'image_path'  => $product['image_path'],
    ];
}

// Shipping: free for orders over RM 150, else RM 10
$shipping = $subtotal >= 150.00 ? 0.00 : 10.00;
$total    = $subtotal + $shipping;

echo json_encode([
    'success'    => true,
    'items'      => $items,
    'subtotal'   => number_format($subtotal, 2),
    'shipping'   => number_format($shipping, 2),
    'total'      => number_format($total, 2),
    'cart_count' => array_sum(array_column($items, 'quantity')),
]);
