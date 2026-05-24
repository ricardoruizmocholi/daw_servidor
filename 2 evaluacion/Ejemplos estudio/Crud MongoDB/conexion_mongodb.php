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

///////////////////
//  Create
//////////////////

// Datos en formato array asociativo (equivalente a un JSON)
$nuevoJuego = [
    "titulo" => "Starfield",
    "precio" => 69.99,
    "genero" => "RPG",
    "plataformas" => ["PC", "Xbox"] // ¡Mongo permite arrays dentro de campos!
];

$resultado = $coleccion->insertOne($nuevoJuego);

echo "Insertado con ID: " . $resultado->getInsertedId();

///////////////
// Read
///////////////

// A. Buscar TODOS
$cursor = $coleccion->find();
foreach ($cursor as $doc) {
    echo $doc["titulo"] . "<br>";
}

// B. Buscar con FILTRO (ej: precio mayor que 50)
// $gt = Greater Than (Mayor que)
$criterio = ["precio" => ['$gt' => 50]];
$juegosCaros = $coleccion->find($criterio);

// C. Buscar uno solo por TITULO
$juego = $coleccion->findOne(["titulo" => "Starfield"]);

/////////////
// Update
///////////

// Filtro: qué documento quiero cambiar
// Actualización: qué campos quiero cambiar
$filtro = ["titulo" => "Starfield"];
$cambios = ['$set' => ["precio" => 49.99]];

$resultado = $coleccion->updateOne($filtro, $cambios);

echo "Documentos sincronizados: " . $resultado->getMatchedCount();

///////////
// Delete
///////////

$coleccion->deleteOne(["titulo" => "Starfield"]);
echo "Borrado correctamente";

/*

⚠️ EL PUNTO MÁS IMPORTANTE DEL EXAMEN: El ObjectId
En MySQL los IDs son números (1, 2, 3...). En MongoDB, los IDs son objetos especiales llamados ObjectId.

Si recibes un ID desde JavaScript (un texto como "652ab3..."), no puedes pasárselo a Mongo directamente como texto. Tienes que convertirlo.

Cómo buscar por ID correctamente:

*/

$idDesdeJS = "652ab3c4d5e6f7a8b9c0d1e2"; // Esto suele venir de un fetch o $_POST

try {
    $juego = $coleccion->findOne([
        "_id" => new MongoDB\BSON\ObjectId($idDesdeJS)
    ]);
    
    if ($juego) {
        echo "Encontrado: " . $juego["titulo"];
    }
} catch (Exception $e) {
    echo "ID no válido";
}

?>