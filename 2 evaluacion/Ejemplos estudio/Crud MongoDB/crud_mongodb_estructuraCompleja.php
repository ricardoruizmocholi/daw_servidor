<?php
<?php
require 'vendor/autoload.php'; // Carga la librería de MongoDB

use MongoDB\Client;
use MongoDB\BSON\ObjectId; // Necesario para buscar por ID

try {
    // 1. Conexión al servidor local
    $client = new Client("mongodb://localhost:27017");

    // 2. Selección de Base de Datos y Colección
    // Si no existen, Mongo las crea automáticamente al insertar datos
    $db = $client->videojuegos_asir;
    $coleccion = $db->juegos;

} catch (Exception $e) {
    die("Error al conectar a MongoDB: " . $e->getMessage());
}

/// Create

$documento = [
    "titulo" => "Cyberpunk 2077",
    "detalles" => [
        "desarrolladora" => "CD Projekt",
        "motor" => "RedEngine"
    ],
    "etiquetas" => ["RPG", "Mundo Abierto", "Sci-Fi"],
    "valoraciones" => [
        ["usuario" => "Alex", "nota" => 9],
        ["usuario" => "Marta", "nota" => 8]
    ]
];

$coleccion->insertOne($documento);

// REad

$juego = $coleccion->findOne(["titulo" => "Cyberpunk 2077"]);

// Acceder al JSON interno (Objeto)
echo $juego["detalles"]["motor"]; // Imprime: RedEngine

// Acceder al primer elemento del Array
echo $juego["etiquetas"][0]; // Imprime: RPG

// Recorrer el array de objetos (valoraciones)
foreach ($juego["valoraciones"] as $v) {
    echo "Usuario: " . $v["usuario"] . " - Nota: " . $v["nota"] . "<br>";
}

// Update

//A- Actualizar un campo del objeto interno:
    //Si quieres cambiar solo el motor dentro de detalles:
$coleccion->updateOne(
    ["titulo" => "Cyberpunk 2077"],
    ['$set' => ["detalles.motor" => "Unreal Engine 5"]] // Notación de punto
);

/*
B. Añadir un elemento a un array ($push):
    Si quieres añadir una nueva etiqueta sin borrar las anteriores:
*/
$coleccion->updateOne(
    ["titulo" => "Cyberpunk 2077"],
    ['$push' => ["etiquetas" => "Shooter"]]
);

/*
C. Quitar un elemento de un array ($pull):
*/

$coleccion->updateOne(
    ["titulo" => "Cyberpunk 2077"],
    ['$pull' => ["etiquetas" => "RPG"]]
);

/*

4. Búsqueda por campos internos
También puedes filtrar por lo que haya dentro del JSON anidado.

*/

// Buscar juegos cuya desarrolladora sea "CD Projekt"
$criterio = ["detalles.desarrolladora" => "CD Projekt"];
$resultados = $coleccion->find($criterio);

// Buscar juegos que tengan la etiqueta "RPG" en su lista
$criterio2 = ["etiquetas" => "RPG"];
$resultados2 = $coleccion->find($criterio2);
