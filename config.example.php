<?php
/**
 * StackSnap · Database Configuration Template
 * -----------------------------------------------------------------------------
 * Copy this file to config.php and set your MySQL database credentials,
 * or configure them through environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASS).
 *
 * IMPORTANT: Never commit config.php containing real credentials to GitHub.
 */

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'stacksnap_db';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

// Allowed CORS origin(s): '*' for all, or comma-separated list (e.g. 'https://sputlab.com, http://localhost:8000')
$allowedOrigin = getenv('ALLOWED_ORIGIN') ?: '*';

if (!defined('DB_HOST')) define('DB_HOST', $dbHost);
if (!defined('DB_NAME')) define('DB_NAME', $dbName);
if (!defined('DB_USER')) define('DB_USER', $dbUser);
if (!defined('DB_PASS')) define('DB_PASS', $dbPass);
if (!defined('ALLOWED_ORIGIN')) define('ALLOWED_ORIGIN', $allowedOrigin);

function stacksnap_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function stacksnap_cors_and_json(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = defined('ALLOWED_ORIGIN') ? ALLOWED_ORIGIN : '*';

    if ($allowed === '*') {
        header('Access-Control-Allow-Origin: *');
    } else {
        $allowedList = array_map('trim', explode(',', $allowed));
        if (in_array($origin, $allowedList, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
        } elseif (!empty($allowedList)) {
            header('Access-Control-Allow-Origin: ' . $allowedList[0]);
        }
    }

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Content-Type: application/json; charset=utf-8');

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
