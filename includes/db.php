<?php
$config = require __DIR__ . '/config.php';

$host = $config['db_host'] ?? 'localhost';
$db   = $config['db_name'] ?? 'barangay_data';
$user = $config['db_user'] ?? 'root';
$pass = $config['db_pass'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
