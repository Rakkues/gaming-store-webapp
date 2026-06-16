<?php

// Headers to allow frontend JavaScript to access this API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 1. Database Configuration
$host = "localhost";
$db_name = "gaming_retail_store";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password is empty

try {
    // 2. Connect to the database using PDO (PHP Data Objects)
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Prepare and execute the SQL query
    $query = "SELECT * FROM products";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // 4. Fetch the data as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Send data back to JavaScript with a 200 OK status
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);
} catch (PDOException $exception) {
    // If something goes wrong, send an error message
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $exception->getMessage()
    ]);
}
