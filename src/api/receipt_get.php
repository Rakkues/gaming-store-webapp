<?php
/**
 * receipt_get.php — Fetch order details for the Receipt page
 * Method: GET
 * Security: only returns orders belonging to the current session
 * 
 * CIT6224 Web Application Development | Member 3 (Cashier)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';
requireSession();

// Resolve order ID: prefer session, allow explicit param for re-print
$order_id = $_SESSION['last_order_id'] ?? (isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0);

if ($order_id <= 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No order found. Please complete checkout first.']);
    exit;
}

$pdo = getDBConnection();

// Fetch order — must belong to this session for security
$stmt = $pdo->prepare('
    SELECT * FROM orders
    WHERE id = :id AND session_id = :sid
    LIMIT 1
');
$stmt->execute([':id' => $order_id, ':sid' => session_id()]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Order not found or access denied.']);
    exit;
}

// Fetch order items
$stmt = $pdo->prepare('
    SELECT oi.*, p.image_path
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = :order_id
');
$stmt->execute([':order_id' => $order_id]);
$items = $stmt->fetchAll();

// Format prices for display
foreach ($items as &$item) {
    $item['product_price'] = number_format((float) $item['product_price'], 2);
    $item['line_total']    = number_format((float) $item['line_total'], 2);
}
unset($item);

// Shipping method display names
$shipping_names = [
    'ninjavan'    => 'NinjaVan',
    'spx_express' => 'SPX Express',
    'jnt_express' => 'J&T Express',
];

// Payment method display names
$payment_names = [
    'credit_card' => 'Credit Card',
    'spaylater'   => 'SPayLater',
    'atome'       => 'Atome',
];

echo json_encode([
    'success' => true,
    'order'   => [
        'id'                   => (int)    $order['id'],
        'order_number'         =>           $order['order_number'],
        'transaction_id'       =>           $order['transaction_id'],
        'tracking_id'          =>           $order['tracking_id'],
        'shipping_method'      =>           $order['shipping_method'],
        'shipping_method_name' =>           $shipping_names[$order['shipping_method']] ?? $order['shipping_method'],
        'payment_method'       =>           $order['payment_method'],
        'payment_method_name'  =>           $payment_names[$order['payment_method']] ?? $order['payment_method'],
        'email'                =>           $order['email'],
        'phone'                =>           $order['phone'],
        'first_name'           =>           $order['first_name'],
        'last_name'            =>           $order['last_name'],
        'address'              =>           $order['address'],
        'address2'             =>           $order['address2'],
        'postcode'             =>           $order['postcode'],
        'city'                 =>           $order['city'],
        'state'                =>           $order['state'],
        'country'              =>           $order['country'],
        'subtotal'             => number_format((float) $order['subtotal'],     2),
        'shipping_fee'         => number_format((float) $order['shipping_fee'], 2),
        'total'                => number_format((float) $order['total'],        2),
        'status'               =>           $order['status'],
        'created_at'           =>           $order['created_at'],
    ],
    'items'   => $items,
]);
