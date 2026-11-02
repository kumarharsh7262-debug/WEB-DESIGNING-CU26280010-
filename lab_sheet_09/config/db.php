<?php
/**
 * Task 2: Database connection (mysqli) with proper error handling.
 * Included on every page through includes/init.php using require_once,
 * so the connection file is loaded only once per request.
 */

// Make mysqli throw exceptions instead of printing warnings
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';            // change for your XAMPP/WAMP/MySQL setup
const DB_NAME = 'webapp_db';

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Log the real reason privately, show a generic message publicly
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection failed. Did you import database.sql and check config/db.php?');
}
