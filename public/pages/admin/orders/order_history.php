<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../../src/config/database.php';

$pdo = getDBConnection();
$search = trim($_GET['search'] ?? '');
$paymentMethod = trim($_GET['payment_method'] ?? '');
$allowedPaymentMethods = ['credit_card', 'spaylater', 'atome'];

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(
        order_number LIKE :search_order_number
        OR transaction_id LIKE :search_transaction_id
        OR email LIKE :search_email
        OR phone LIKE :search_phone
        OR address LIKE :search_address
        OR city LIKE :search_city
        OR state LIKE :search_state
    )';
    $params[':search_order_number'] = '%' . $search . '%';
    $params[':search_transaction_id'] = '%' . $search . '%';
    $params[':search_email'] = '%' . $search . '%';
    $params[':search_phone'] = '%' . $search . '%';
    $params[':search_address'] = '%' . $search . '%';
    $params[':search_city'] = '%' . $search . '%';
    $params[':search_state'] = '%' . $search . '%';
}

if ($paymentMethod !== '' && in_array($paymentMethod, $allowedPaymentMethods, true)) {
    $where[] = 'payment_method = :payment_method';
    $params[':payment_method'] = $paymentMethod;
} else {
    $paymentMethod = '';
}

$query = "
    SELECT
        id,
        order_number,
        transaction_id,
        email,
        phone,
        address,
        address2,
        postcode,
        city,
        state,
        country,
        payment_method,
        total,
        status,
        created_at
    FROM orders
";

if (!empty($where)) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}

$query .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order History — Gaming Store Admin</title>
    <link rel="stylesheet" href="../../../css/style.css" />
    <link rel="stylesheet" href="../../../css/index.css" />
  </head>
  <body>
    <?php include "../../components/admin_header.php" ?>

    <main class="admin-page">
      <h1 class="featured-product-title">Order History</h1>
      <p>View completed purchase records.</p>

      <form class="admin-filter-form order-filter-form" method="GET" action="order_history.php">
        <div class="admin-filter-field">
          <label for="search">Search</label>
          <input
            type="text"
            id="search"
            name="search"
            placeholder="Order number, transaction, email, phone, or address"
            value="<?= htmlspecialchars($search) ?>"
          />
        </div>

        <div class="admin-filter-field">
          <label for="payment_method">Payment</label>
          <select id="payment_method" name="payment_method">
            <option value="">All payments</option>
            <?php foreach ($allowedPaymentMethods as $option) : ?>
              <option value="<?= htmlspecialchars($option) ?>" <?= $paymentMethod === $option ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $option))) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="admin-filter-actions">
          <button type="submit">Apply</button>
          <a href="order_history.php">Reset</a>
        </div>
      </form>

      <p class="admin-muted"><?= count($orders) ?> order<?= count($orders) === 1 ? '' : 's' ?> found.</p>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order Number</th>
              <th>Transaction ID</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Payment</th>
              <th>Total</th>
              <th>Status</th>
              <th>Purchased Time</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($orders)) : ?>
              <tr>
                <td colspan="8">No orders found.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($orders as $order) : ?>
              <?php
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
              <tr>
                <td>
                  <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                  <p class="admin-muted">
                    <a href="view_order.php?id=<?= urlencode((string) $order['id']) ?>">View Details</a>
                  </p>
                </td>
                <td><?= htmlspecialchars($order['transaction_id']) ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['phone']) ?></td>
                <td><?= htmlspecialchars($fullAddress) ?></td>
                <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></td>
                <td>RM <?= htmlspecialchars(number_format((float) $order['total'], 2)) ?></td>
                <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>
