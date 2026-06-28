<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../../src/config/database.php';

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($orderId <= 0) {
    header("Location: order_history.php?error=" . urlencode("Invalid order."));
    exit();
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: order_history.php?error=" . urlencode("Order not found."));
    exit();
}

$stmt = $pdo->prepare("
    SELECT product_name, product_price, quantity, line_total
    FROM order_items
    WHERE order_id = ?
    ORDER BY id ASC
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

$addressParts = array_filter([
    $order['address'],
    $order['address2'],
    $order['postcode'],
    $order['city'],
    $order['state'],
    $order['country'],
]);
$fullAddress = implode(', ', $addressParts);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Details — Gaming Store Admin</title>
    <link rel="stylesheet" href="../../../css/style.css" />
    <link rel="stylesheet" href="../../../css/index.css" />
  </head>
  <body>
    <?php include "../../components/admin_header.php" ?>

    <main class="admin-page">
      <h1 class="featured-product-title">Order Details</h1>

      <p>
        <a class="admin-primary-link" href="order_history.php">Back to Order History</a>
      </p>

      <div class="admin-detail-grid">
        <section>
          <h2>Order</h2>
          <p><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
          <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
          <p><strong>Purchased Time:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
          <p><strong>Payment:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></p>
          <p><strong>Total:</strong> RM <?= htmlspecialchars(number_format((float) $order['total'], 2)) ?></p>
        </section>

        <section>
          <h2>Customer</h2>
          <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
          <p><strong>Address:</strong> <?= htmlspecialchars($fullAddress) ?></p>
        </section>
      </div>

      <h2>Purchased Items</h2>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Line Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($items)) : ?>
              <tr>
                <td colspan="4">No purchased items found for this order.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($items as $item) : ?>
              <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td>RM <?= htmlspecialchars(number_format((float) $item['product_price'], 2)) ?></td>
                <td><?= htmlspecialchars((string) $item['quantity']) ?></td>
                <td>RM <?= htmlspecialchars(number_format((float) $item['line_total'], 2)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>
