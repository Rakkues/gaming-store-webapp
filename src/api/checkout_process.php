<?php
/**
 * checkout_process.php — Validate order, calculate totals, save to DB
 * Method: POST
 * All financial calculations done server-side ONLY.
 * 
 * CIT6224 Web Application Development | Member 3 (Cashier)
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

// ============================================================
// 1. Server-side Validation
// ============================================================
$errors = [];

$fields = [
    'email'           => 'Email address',
    'first_name'      => 'First name',
    'last_name'       => 'Last name',
    'address'         => 'Address',
    'postcode'        => 'Postcode',
    'city'            => 'City',
    'state'           => 'State',
    'phone'           => 'Phone number',
    'shipping_method' => 'Shipping method',
    'payment_method'  => 'Payment method',
];

$data = [];
foreach ($fields as $key => $label) {
    $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
    if ($value === '') {
        $errors[$key] = "$label is required.";
    } else {
        $data[$key] = $value;
    }
}

// Optional field
$data['address2'] = isset($_POST['address2']) ? trim($_POST['address2']) : '';

// Regex validations (only if field is present)
if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}

// Malaysian postcode: exactly 5 digits
if (!empty($data['postcode']) && !preg_match('/^\d{5}$/', $data['postcode'])) {
    $errors['postcode'] = 'Postcode must be exactly 5 digits.';
}

// Malaysian phone: starts with 01, 8–11 digits total
if (!empty($data['phone']) && !preg_match('/^01\d{7,9}$/', preg_replace('/[\s\-]/', '', $data['phone']))) {
    $errors['phone'] = 'Please enter a valid Malaysian phone number (e.g. 0123456789).';
}

// Allowed shipping methods
$allowed_shipping = ['ninjavan', 'spx_express', 'jnt_express'];
if (!empty($data['shipping_method']) && !in_array($data['shipping_method'], $allowed_shipping, true)) {
    $errors['shipping_method'] = 'Invalid shipping method selected.';
}

// Allowed payment methods
$allowed_payments = ['credit_card', 'spaylater', 'atome'];
if (!empty($data['payment_method']) && !in_array($data['payment_method'], $allowed_payments, true)) {
    $errors['payment_method'] = 'Invalid payment method selected.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ============================================================
// 2. Resolve Checkout Items
// ============================================================
$pdo = getDBConnection();
$order_items = [];
$subtotal    = 0.0;
$checkout_mode = $_POST['checkout_mode'] ?? 'cart';

if ($checkout_mode === 'buy_now') {
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    $quantity = max(1, $quantity);

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product.']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, name, price, stock FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product is no longer available.']);
        exit;
    }

    if ($quantity > (int) $product['stock']) {
        $errors['stock'] = htmlspecialchars($product['name']) . ' only has ' . $product['stock'] . ' unit(s) left.';
    } else {
        $line_total = round((float) $product['price'] * $quantity, 2);
        $subtotal += $line_total;
        $order_items[] = [
            'product_id'    => (int) $product['id'],
            'product_name'  => $product['name'],
            'product_price' => (float) $product['price'],
            'quantity'      => $quantity,
            'line_total'    => $line_total,
        ];
    }
} else {
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
        exit;
    }

    $ids = implode(',', array_map('intval', array_keys($cart)));
    $stmt = $pdo->query("SELECT id, name, price, stock FROM products WHERE id IN ($ids)");
    $products = [];
    foreach ($stmt->fetchAll() as $row) {
        $products[$row['id']] = $row;
    }

    foreach ($cart as $pid => $entry) {
        $product = $products[$pid] ?? null;
        if (!$product) {
            $errors['stock'] = 'A product in your cart is no longer available.';
            break;
        }
        if ($entry['quantity'] > $product['stock']) {
            $errors['stock'] = htmlspecialchars($product['name']) . ' only has ' . $product['stock'] . ' unit(s) left.';
            break;
        }
        $line_total    = round((float) $product['price'] * $entry['quantity'], 2);
        $subtotal     += $line_total;
        $order_items[] = [
            'product_id'    => (int) $pid,
            'product_name'  => $product['name'],
            'product_price' => (float) $product['price'],
            'quantity'      => $entry['quantity'],
            'line_total'    => $line_total,
        ];
    }
}

if (empty($order_items) && empty($errors)) {
    $errors['stock'] = 'No valid checkout items found.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ============================================================
// 4. Calculate Shipping Fee (carrier-based)
// ============================================================
$shipping_fees = [
    'ninjavan'    => 5.90,
    'spx_express' => 3.90,
    'jnt_express' => 6.90,
];

// Free shipping for orders >= RM 150
if ($subtotal >= 150.00) {
    $shipping_fee = 0.00;
} else {
    $shipping_fee = $shipping_fees[$data['shipping_method']] ?? 5.90;
}

$total = round($subtotal + $shipping_fee, 2);

// ============================================================
// 5. Generate Order Number (DD/MM/YYYY/XXX)
// ============================================================
$stmt = $pdo->prepare('SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()');
$stmt->execute();
$daily_count  = (int) $stmt->fetch()['count'] + 1;
$order_number = date('d/m/Y') . '/' . str_pad($daily_count, 3, '0', STR_PAD_LEFT);

// ============================================================
// 6. Generate Transaction ID (TXN-YYYYMMDD-XXXXX)
// ============================================================
$stmt = $pdo->query('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM orders');
$next_id       = (int) $stmt->fetch()['next_id'];
$transaction_id = 'TXN-' . date('Ymd') . '-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);

// ============================================================
// 7. Generate Tracking ID (carrier-prefixed)
// ============================================================
$tracking_prefixes = [
    'ninjavan'    => 'NV',
    'spx_express' => 'SPX',
    'jnt_express' => 'JT',
];
$prefix    = $tracking_prefixes[$data['shipping_method']] ?? 'TRK';
$digits    = '';
for ($i = 0; $i < 12; $i++) {
    $digits .= mt_rand(0, 9);
}
$tracking_id = $prefix . $digits;

// ============================================================
// 8. Save Order to Database (Atomic Transaction)
// ============================================================
try {
    $pdo->beginTransaction();

    // Insert order
    $stmt = $pdo->prepare('
        INSERT INTO orders
            (order_number, transaction_id, session_id, email, phone,
             first_name, last_name, address, address2, postcode, city, state, country,
             shipping_method, tracking_id, payment_method,
             subtotal, shipping_fee, total)
        VALUES
            (:order_number, :transaction_id, :session_id, :email, :phone,
             :first_name, :last_name, :address, :address2, :postcode, :city, :state, :country,
             :shipping_method, :tracking_id, :payment_method,
             :subtotal, :shipping_fee, :total)
    ');

    $stmt->execute([
        ':order_number'    => $order_number,
        ':transaction_id'  => $transaction_id,
        ':session_id'      => session_id(),
        ':email'           => $data['email'],
        ':phone'           => preg_replace('/[\s\-]/', '', $data['phone']),
        ':first_name'      => $data['first_name'],
        ':last_name'       => $data['last_name'],
        ':address'         => $data['address'],
        ':address2'        => $data['address2'],
        ':postcode'        => $data['postcode'],
        ':city'            => $data['city'],
        ':state'           => $data['state'],
        ':country'         => 'Malaysia',
        ':shipping_method' => $data['shipping_method'],
        ':tracking_id'     => $tracking_id,
        ':payment_method'  => $data['payment_method'],
        ':subtotal'        => $subtotal,
        ':shipping_fee'    => $shipping_fee,
        ':total'           => $total,
    ]);

    $order_id = (int) $pdo->lastInsertId();

    // Insert order items & decrement stock
    $item_stmt  = $pdo->prepare('
        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, line_total)
        VALUES (:order_id, :product_id, :product_name, :product_price, :quantity, :line_total)
    ');
    $stock_stmt = $pdo->prepare('UPDATE products SET stock = stock - :qty1 WHERE id = :id AND stock >= :qty2');

    foreach ($order_items as $item) {
        $item_stmt->execute([
            ':order_id'      => $order_id,
            ':product_id'    => $item['product_id'],
            ':product_name'  => $item['product_name'],
            ':product_price' => $item['product_price'],
            ':quantity'      => $item['quantity'],
            ':line_total'    => $item['line_total'],
        ]);

        $stock_stmt->execute([
            ':qty1' => $item['quantity'],
            ':qty2' => $item['quantity'],
            ':id'   => $item['product_id'],
        ]);
        if ($stock_stmt->rowCount() === 0) {
            throw new RuntimeException('Stock mismatch for product ID ' . $item['product_id']);
        }
    }

    $pdo->commit();

    // Success: store order ID in session. Cart checkout clears the cart;
    // buy-now checkout leaves the cart unchanged.
    $_SESSION['last_order_id']     = $order_id;
    $_SESSION['last_order_number'] = $order_number;
    if ($checkout_mode !== 'buy_now') {
        $_SESSION['cart'] = [];
        if (isset($_SESSION['user_id'])) {
            $stmtClear = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmtClear->execute([$_SESSION['user_id']]);
        }
    }

    echo json_encode([
        'success'        => true,
        'order_id'       => $order_id,
        'order_number'   => $order_number,
        'transaction_id' => $transaction_id,
        'redirect'       => '/pages/shopping/receipt.html',
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Checkout error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Order processing failed. Please try again.']);
}
