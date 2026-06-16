<?php
/**
 * Database Configuration
 * CIT6224 Web Application Development
 * Gaming Store — XAMPP/MySQL Connection
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'gaming_retail_store');
define('DB_USER', 'root');
define('DB_PASS', '');          // Default XAMPP has no root password
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO connection.
 * Uses exceptions for error handling.
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Do NOT expose DB errors to the client
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
            exit;
        }
    }

    return $pdo;
}

/**
 * Start or resume a PHP session (called by all API endpoints).
 */
function requireSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Initialise cart if not present
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}
