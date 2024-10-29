<?php
// Definición de variables 
$host = 'localhost';
$db = 'login_system';
$user = 'root'; // Por defecto en XAMPP/WAMP
$pass = ''; // Contraseña por defecto en XAMPP/WAMP

try {
    // Establecer la conexión a la base de datos, permitiendo la ejecución de consultas de manera segura.
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
