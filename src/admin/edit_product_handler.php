<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['admin_product_token'])) {
    $_SESSION['admin_product_token'] = bin2hex(random_bytes(32));
}

$allowedCategories = ['mouse', 'keyboard', 'audio', 'collectibles', 'merchandise'];
$pdo = getDBConnection();
$productId = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);
$errors = [];

if ($productId <= 0) {
    header("Location: inventory.php?error=" . urlencode("Invalid product."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['admin_product_token'], $submittedToken)) {
        $errors[] = "Invalid request. Please refresh and try again.";
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $imagePath = trim($_POST['image_path'] ?? '');

    if ($name === '') {
        $errors[] = "Product name is required.";
    }
    if ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $errors[] = "Price must be a valid number greater than or equal to 0.";
    }
    if ($stock === '' || filter_var($stock, FILTER_VALIDATE_INT) === false || (int) $stock < 0) {
        $errors[] = "Stock must be a whole number greater than or equal to 0.";
    }
    if (!in_array($category, $allowedCategories, true)) {
        $errors[] = "Please select a valid category.";
    }
    if ($imagePath === '') {
        $errors[] = "Image path is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE products
                SET name = ?, description = ?, price = ?, stock = ?, category = ?, image_path = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $name,
                $description,
                number_format((float) $price, 2, '.', ''),
                (int) $stock,
                $category,
                $imagePath,
                $productId,
            ]);

            header("Location: inventory.php?success=" . urlencode("Product updated."));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = "A product with that name already exists.";
            } else {
                $errors[] = "Product update failed.";
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT id, name, description, price, stock, category, image_path FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: inventory.php?error=" . urlencode("Product not found."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = [
        'id' => $productId,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'stock' => $stock,
        'category' => $category,
        'image_path' => $imagePath,
    ];
}
