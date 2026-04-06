<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS beninmarket CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "OK — Base de donnees 'beninmarket' creee avec succes!\n";
} catch (PDOException $e) {
    echo "ERREUR PDO: " . $e->getMessage() . "\n";
    echo "Verifier que MySQL tourne sur le port 3306.\n";
}
