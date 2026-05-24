<?php

$precioMax = 60;

// 1. Preparar y ejecutar
$stmt = $pdo->prepare("SELECT * FROM videojuego WHERE precio_base < :precio");
$stmt->execute([':precio' => $precioMax]);

// 2. fetchAll(PDO::FETCH_ASSOC) devuelve un ARRAY BIDIMENSIONAL
// Cada fila es un array asociativo donde las claves son los nombres de las columnas.
$videojuegos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Creamos un array asociativo de "Respuesta"
$miRespuesta = [
    "estado" => "exito",
    "total_encontrados" => count($videojuegos),
    "lista_juegos" => $videojuegos // Metemos el array de la DB dentro del nuestro
];

// --- PASOS PARA DEVOLVER JSON ---

// 1. Cabecera obligatoria: le dice al navegador "Oye, esto es un JSON, no un HTML"
header("Content-Type: application/json; charset=utf-8");

// 2. Convertimos el array a texto JSON y lo imprimimos
// json_encode hace el trabajo sucio de convertir tipos de PHP a tipos de JS
echo json_encode($miRespuesta);

// 3. Cortamos la ejecución para asegurar que no se envíe nada más (opcional pero recomendado)
exit;




?>