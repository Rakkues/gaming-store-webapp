<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';

$category = isset($_GET['search']) ? trim($_GET['search']) : '';

function fetchProducts($category): array
{
    try {
        $conn = getDBConnection();
        if ($category == '') {
            $query = "SELECT * FROM products";
            $stmt = $conn->prepare($query);
        } else {
            $query = "SELECT * FROM products WHERE category LIKE :category";
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        }

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $products;
    } catch (PDOException $exception) {
        error_log("Database error in fetchProducts: " . $exception->getMessage());
        return [];
    }
}
