<?php require_once __DIR__ . '/../../../../src/admin/add_product_handler.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Product — Gaming Store Admin</title>
    <link rel="stylesheet" href="../../../css/style.css" />
    <link rel="stylesheet" href="../../../css/index.css" />
  </head>
  <body>
    <?php include "../../components/admin_header.php" ?>

    <main class="admin-page">
      <h1 class="featured-product-title">Add Product</h1>

      <?php if (!empty($errors)) : ?>
        <div class="admin-alert error">
          <?php foreach ($errors as $error) : ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="admin-edit-form" method="POST" action="add_product.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_product_token']) ?>">

        <label>
          Name
          <input type="text" name="name" required maxlength="255" value="<?= htmlspecialchars($product['name']) ?>">
        </label>

        <label>
          Description
          <textarea name="description" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
        </label>

        <label>
          Price
          <input type="number" name="price" required min="0" step="0.01" value="<?= htmlspecialchars((string) $product['price']) ?>">
        </label>

        <label>
          Stock
          <input type="number" name="stock" required min="0" step="1" value="<?= htmlspecialchars((string) $product['stock']) ?>">
        </label>

        <label>
          Category
          <select name="category" required>
            <option value="">Select category</option>
            <?php foreach ($allowedCategories as $option) : ?>
              <option value="<?= htmlspecialchars($option) ?>" <?= $product['category'] === $option ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($option)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>
          Image Path
          <input type="text" name="image_path" required maxlength="512" value="<?= htmlspecialchars($product['image_path']) ?>">
        </label>

        <div class="admin-form-actions">
          <button type="submit">Add Product</button>
          <a href="../inventory.php">Cancel</a>
        </div>
      </form>
    </main>
  </body>
</html>
