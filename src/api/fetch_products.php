<?php

header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../config/database.php';

function fetchProducts($category, $sortBy = 'default'): array
{
    try {
        $conn = getDBConnection();

        $sortStrategies = [
        'alphabetical' => 'name ASC',
        'newly_added' => 'created_at DESC',
        'default' => 'id DESC'
        ];

        $orderByClause = $sortStrategies[$sortBy] ?? $sortStrategies['default'];

        if ($category == '') {
            $query = "SELECT * FROM products ORDER BY $orderByClause";
            $stmt = $conn->prepare($query);
        } else {
            $query = "SELECT * FROM products WHERE category LIKE :category ORDER BY $orderByClause";
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
