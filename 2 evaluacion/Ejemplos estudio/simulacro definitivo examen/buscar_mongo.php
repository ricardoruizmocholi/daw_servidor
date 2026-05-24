<?php
header("Content-Type: application/json; charset=utf-8");
require 'vendor/autoload.php';
use MongoDB\Client;

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Parseo de los datos del frontend
$precioMax = (float)($data["precio_max"] ?? 0);
$anioMin = (int)($data["anio_min"] ?? 0);
$fechaMin = $anioMin . "-12-31"; 

try {
    $client = new Client("mongodb://localhost:27017");
    $coleccion = $client->Videojuegos->JuegosBase;

    // TODO 13: Crear el array de filtro para MongoDB.
    // - El campo 'precio_base' debe ser MENOR O IGUAL ($lte) a $precioMax.
    // - El campo 'fecha_lanzamiento' debe ser MAYOR ($gt) a $fechaMin.
    $filtro = [
        "precio_base"=> ['$lte'=>$precioMax],
        "fecha_lanzamiento" => ['gt'=>$fechaMin]
    ];

    // TODO 14: Ejecutar la consulta en la colección pasando el filtro.
    $cursor = $coleccion->find($filtro); 

    $juegos = [];

    // TODO 15: Recorrer el cursor. 
    // De cada documento, extraer 'titulo', 'fecha_lanzamiento' y 'precio_base'.
    // Si algún campo no existe, ponle "Dato no disponible" por defecto (operador ??).
    // Guardarlo en el array $juegos.
    foreach($cursor as $documento){
        $juegos[] = [
            "id_mongo"=>(string)$documento['_id'],
            "titulo"=>$documento['titulo'] ?? "Sin titulo",
            "fecha_lanzamiento"=>$documento['fecha_lanzamiento'] ?? "No consta la fecha de lanzamiento",
            "precio_base"=>$documento["precio_base"] ?? 0.0
        ];
    }

    // TODO 16: Imprimir el $juegos en formato JSON.
    echo json_encode($juegos);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}