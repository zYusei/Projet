<?php

$DB_HOST = 'localhost';
$DB_NAME = 'funcodelab';
$DB_USER = 'root';
$DB_PASS = ''; // vide par défaut sous XAMPP

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  http_response_code(500);
  die('Erreur DB: ' . htmlspecialchars($e->getMessage()));
}

if (session_status() === PHP_SESSION_NONE) session_start();


$badges = [
    ['user' => 'Alice', 'badge' => 'Maître du JavaScript', 'date' => '2025-08-01'],
    ['user' => 'Bob', 'badge' => 'Explorateur SQL', 'date' => '2025-08-03'],
    ['user' => 'Claire', 'badge' => 'Débuggeur Pro', 'date' => '2025-08-05'],
];
?>
