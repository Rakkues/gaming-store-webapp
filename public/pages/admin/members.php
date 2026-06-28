<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../src/config/database.php';

$pdo = getDBConnection();
$search = trim($_GET['search'] ?? '');
$userType = trim($_GET['usertype'] ?? '');
$allowedUserTypes = ['admin', 'customer'];

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(username LIKE :search_username OR email LIKE :search_email)';
    $params[':search_username'] = '%' . $search . '%';
    $params[':search_email'] = '%' . $search . '%';
}

if ($userType !== '' && in_array($userType, $allowedUserTypes, true)) {
    $where[] = 'usertype = :usertype';
    $params[':usertype'] = $userType;
} else {
    $userType = '';
}

$query = "
    SELECT userid, username, email, usertype
    FROM users
";

if (!empty($where)) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}

$query .= ' ORDER BY userid DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$members = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Members — Gaming Store Admin</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../css/index.css" />
  </head>
  <body>
    <?php include "../components/admin_header.php" ?>

    <main class="admin-page">
      <h1 class="featured-product-title">Members</h1>
      <p>View registered customers and admin accounts.</p>

      <form class="admin-filter-form member-filter-form" method="GET" action="members.php">
        <div class="admin-filter-field">
          <label for="search">Search</label>
          <input
            type="text"
            id="search"
            name="search"
            placeholder="Username or email"
            value="<?= htmlspecialchars($search) ?>"
          />
        </div>

        <div class="admin-filter-field">
          <label for="usertype">User Type</label>
          <select id="usertype" name="usertype">
            <option value="">All users</option>
            <?php foreach ($allowedUserTypes as $option) : ?>
              <option value="<?= htmlspecialchars($option) ?>" <?= $userType === $option ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($option)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="admin-filter-actions">
          <button type="submit">Apply</button>
          <a href="members.php">Reset</a>
        </div>
      </form>

      <p class="admin-muted"><?= count($members) ?> member<?= count($members) === 1 ? '' : 's' ?> found.</p>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>User Type</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($members)) : ?>
              <tr>
                <td colspan="4">No members found.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($members as $member) : ?>
              <tr>
                <td><?= htmlspecialchars((string) $member['userid']) ?></td>
                <td><?= htmlspecialchars($member['username']) ?></td>
                <td><?= htmlspecialchars($member['email']) ?></td>
                <td><?= htmlspecialchars(ucfirst($member['usertype'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>
