<?php

require 'vendor/autoload.php';
use MongoDB\BSON\ObjectId;

$id = "64b0f9..."; 
$campo = "precio"; // Dinámico
$valor = 49.99;

$collection = (new MongoDB\Client("mongodb://localhost:27017"))->Videojuegos->Juegos;

// En Mongo es tan simple como pasar la variable como clave del array
$resultado = $collection->updateOne(
    ["_id" => new ObjectId($id)],
    ['$set' => [ $campo => $valor ]] 
);

echo "Documentos modificados: " . $resultado->getModifiedCount();