<?php
$host = "127.0.0.1";
$port = "3306"; 
$dbname = "videojuegos_asir"; //Nombre de la base de datos
$user = "root";//por xampp
$pass = "";//por xampp

try {
    // DSN: Data Source Name (Configuración de la conexión)
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $user, $pass);
    
    // Configuración de errores: lanza una excepción si algo falla (crucial para try-catch)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>