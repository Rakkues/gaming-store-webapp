<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once 'fetch_products.php';

$category = $_GET['category'] ?? '';
$products = fetchProducts($category);

// Output as JSON for your JS fetch() calls
echo json_encode($products);
