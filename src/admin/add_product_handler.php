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
$errors = [];
$product = [
    'name' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'category' => '',
    'image_path' => '/assets/product-images/product-1.webp',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['admin_product_token'], $submittedToken)) {
        $errors[] = "Invalid request. Please refresh and try again.";
    }

    $product = [
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => trim($_POST['price'] ?? ''),
        'stock' => trim($_POST['stock'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'image_path' => trim($_POST['image_path'] ?? ''),
    ];

    if ($product['name'] === '') {
        $errors[] = "Product name is required.";
    }
    if ($product['price'] === '' || !is_numeric($product['price']) || (float) $product['price'] < 0) {
        $errors[] = "Price must be a valid number greater than or equal to 0.";
    }
    if ($product['stock'] === '' || filter_var($product['stock'], FILTER_VALIDATE_INT) === false || (int) $product['stock'] < 0) {
        $errors[] = "Stock must be a whole number greater than or equal to 0.";
    }
    if (!in_array($product['category'], $allowedCategories, true)) {
        $errors[] = "Please select a valid category.";
    }
    if ($product['image_path'] === '') {
        $errors[] = "Image path is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, stock, category, image_path)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $product['name'],
                $product['description'],
                number_format((float) $product['price'], 2, '.', ''),
                (int) $product['stock'],
                $product['category'],
                $product['image_path'],
            ]);

            header("Location: ../inventory.php?success=" . urlencode("Product added."));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = "A product with that name already exists.";
            } else {
                $errors[] = "Product creation failed.";
            }
        }
    }
}
