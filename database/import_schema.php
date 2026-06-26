<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script can only be run from the command line." . PHP_EOL;
    exit(1);
}

require_once __DIR__ . '/../src/config/database.php';

$schemaPath = __DIR__ . '/cashier_schema.sql';

if (!is_file($schemaPath)) {
    fwrite(STDERR, "Schema file not found: {$schemaPath}" . PHP_EOL);
    exit(1);
}

$sql = file_get_contents($schemaPath);

if ($sql === false || trim($sql) === '') {
    fwrite(STDERR, "Schema file is empty or cannot be read." . PHP_EOL);
    exit(1);
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($mysqli->connect_error) {
    fwrite(STDERR, "Database connection failed: {$mysqli->connect_error}" . PHP_EOL);
    exit(1);
}

if (!$mysqli->multi_query($sql)) {
    fwrite(STDERR, "Schema import failed: {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(1);
}

do {
    if ($result = $mysqli->store_result()) {
        $result->free();
    }
} while ($mysqli->more_results() && $mysqli->next_result());

if ($mysqli->errno) {
    fwrite(STDERR, "Schema import failed: {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(1);
}

if (!$mysqli->select_db(DB_NAME)) {
    fwrite(STDERR, "Could not select database " . DB_NAME . ": {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(1);
}

$dedupeSql = "
    DELETE p1 FROM products p1
    JOIN products p2
        ON p1.name = p2.name
        AND p1.id > p2.id
";

if (!$mysqli->query($dedupeSql)) {
    fwrite(STDERR, "Product duplicate cleanup failed: {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(1);
}

$indexResult = $mysqli->query("SHOW INDEX FROM products WHERE Key_name = 'uq_products_name'");

if ($indexResult === false) {
    fwrite(STDERR, "Product index check failed: {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(1);
}

$hasProductNameIndex = $indexResult->num_rows > 0;
$indexResult->free();

if (!$hasProductNameIndex && !$mysqli->query("ALTER TABLE products ADD UNIQUE KEY uq_products_name (name)")) {
    fwrite(STDERR, "Product unique index creation failed: {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(1);
}

$mysqli->close();

echo "Schema imported successfully from {$schemaPath}" . PHP_EOL;
