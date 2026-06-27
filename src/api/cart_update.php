<?php
/**
 * cart_update.php — Modify quantity of a cart item, or remove it
 * Method: POST
 * Body:   product_id (int), action ('increment' | 'decrement' | 'remove')
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

$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$action     = isset($_POST['action'])     ? trim($_POST['action'])      : '';

if ($product_id <= 0 || !in_array($action, ['increment', 'decrement', 'remove'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$cart = &$_SESSION['cart'];

if (!isset($cart[$product_id])) {
    echo json_encode(['success' => false, 'message' => 'Item not in cart.']);
    exit;
}

if ($action === 'remove') {
    unset($cart[$product_id]);
} elseif ($action === 'increment') {
    // Re-check stock from DB before incrementing
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product || $cart[$product_id]['quantity'] >= $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Maximum stock reached.']);
        exit;
    }
    $cart[$product_id]['quantity']++;
} elseif ($action === 'decrement') {
    if ($cart[$product_id]['quantity'] <= 1) {
        unset($cart[$product_id]);
    } else {
        $cart[$product_id]['quantity']--;
    }
}

// --- Sync to DB if logged in ---
if (isset($_SESSION['user_id'])) {
    $pdo = getDBConnection();
    if (!isset($cart[$product_id])) {
        $stmtSync = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmtSync->execute([$_SESSION['user_id'], $product_id]);
    } else {
        $stmtSync = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmtSync->execute([$cart[$product_id]['quantity'], $_SESSION['user_id'], $product_id]);
    }
}

// Recalculate subtotal
$subtotal = 0.0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$cart_count = array_sum(array_column($cart, 'quantity'));

echo json_encode([
    'success'    => true,
    'cart'       => array_values($cart),
    'subtotal'   => number_format($subtotal, 2),
    'cart_count' => $cart_count,
]);
