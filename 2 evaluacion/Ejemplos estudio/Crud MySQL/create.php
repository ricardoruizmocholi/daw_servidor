<?php


//Datos que suelen venir de un $_POST o un fetch JSON
$titulo = "Elden Ring";
$precio = 59.99;

// 1. Preparamos la plantilla (sin los datos reales aún)
$sql = "INSERT INTO videojuego (titulo, precio_base) VALUES (:titulo, :precio)";
$stmt = $pdo->prepare($sql);

// 2. Ejecutamos pasando los datos en un array asociativo
$stmt->execute([
    ':titulo' => $titulo,
    ':precio' => $precio
]);

echo "ID insertado: " . $pdo->lastInsertId();

?>
