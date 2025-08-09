<?php
require 'db.php'; // connexion PDO $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $email === '' || $password === '') {
    exit('Tous les champs sont obligatoires.');
}

// Hash du mot de passe
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insertion
$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
if ($stmt->execute([$username, $email, $hash])) {
    header('Location: connexion.php'); // redirige vers login
    exit;
} else {
    exit('Erreur lors de l\'inscription.');
}
