<?php
$host = 'localhost';
$db = 'login_system';
$user = 'root'; // Por defecto en XAMPP/WAMP
$pass = ''; // Contraseña por defecto en XAMPP/WAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

