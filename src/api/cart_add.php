<?php
/**
 * cart_add.php — Add a product to the session cart
 * Method: POST
 * Body:   product_id (int), quantity (int, optional, default 1)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
requireSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// --- Input sanitisation ---
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity   = isset($_POST['quantity'])   ? (int) $_POST['quantity']   : 1;

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    exit;
}

// --- Fetch product from DB ---
$pdo  = getDBConnection();
$stmt = $pdo->prepare('SELECT id, name, price, stock, image_path FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

// --- Stock check ---
$cart         = &$_SESSION['cart'];
$current_qty  = $cart[$product_id]['quantity'] ?? 0;
$requested    = $current_qty + $quantity;

if ($requested > $product['stock']) {
    echo json_encode([
        'success' => false,
        'message' => 'Insufficient stock. Only ' . $product['stock'] . ' unit(s) available.'
    ]);
    exit;
}

// --- Update session cart ---
if (isset($cart[$product_id])) {
    $cart[$product_id]['quantity'] = $requested;
} else {
    $cart[$product_id] = [
        'product_id'  => $product['id'],
        'name'        => $product['name'],
        'price'       => (float) $product['price'],
        'quantity'    => $quantity,
        'image_path'  => $product['image_path'],
    ];
}

$cart_count = array_sum(array_column($cart, 'quantity'));

echo json_encode([
    'success'    => true,
    'message'    => htmlspecialchars($product['name']) . ' added to cart.',
    'cart_count' => $cart_count,
]);
