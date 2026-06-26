<?php

/**
 * buy_now_get.php - Return a single product formatted as a checkout summary.
 * Method: GET
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
requireSession();

$productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
$quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 1;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$quantity = max(1, $quantity);

$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT id, name, price, stock, image_path FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

if ((int) $product['stock'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Product is sold out.']);
    exit;
}

$quantity = min($quantity, (int) $product['stock']);
$lineTotal = round((float) $product['price'] * $quantity, 2);

echo json_encode([
    'success' => true,
    'items' => [[
        'product_id' => (int) $product['id'],
        'name' => $product['name'],
        'price' => number_format((float) $product['price'], 2),
        'quantity' => $quantity,
        'stock' => (int) $product['stock'],
        'line_total' => number_format($lineTotal, 2),
        'image_path' => $product['image_path'],
    ]],
    'subtotal' => number_format($lineTotal, 2),
    'shipping' => '0.00',
    'total' => number_format($lineTotal, 2),
    'cart_count' => $quantity,
]);
