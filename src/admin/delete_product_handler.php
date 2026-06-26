<?php
session_start();

if (($_SESSION['usertype'] ?? '') !== 'admin') {
    header("Location: /gaming-store-webapp/public/pages/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../inventory.php?error=" . urlencode("Invalid request."));
    exit();
}

$submittedToken = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['admin_product_token']) || !hash_equals($_SESSION['admin_product_token'], $submittedToken)) {
    header("Location: ../inventory.php?error=" . urlencode("Invalid request. Please refresh and try again."));
    exit();
}

$productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($productId <= 0) {
    header("Location: ../inventory.php?error=" . urlencode("Invalid product."));
    exit();
}

require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);

    if ($stmt->rowCount() === 0) {
        header("Location: ../inventory.php?error=" . urlencode("Product not found."));
        exit();
    }

    header("Location: ../inventory.php?success=" . urlencode("Product removed."));
    exit();
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        header("Location: ../inventory.php?error=" . urlencode("Cannot remove a product that appears in order history."));
    } else {
        header("Location: ../inventory.php?error=" . urlencode("Product removal failed."));
    }
    exit();
}
