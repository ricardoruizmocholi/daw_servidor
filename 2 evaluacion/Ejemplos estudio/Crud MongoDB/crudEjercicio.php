<?php

require 'vendor/autoload.php';
use MongoDB\Client;
// Configura typeMap para obtener arrays asociativos limpios
$client = new Client("mongodb://localhost:27017", [], [
    'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']
]);

$db = $client->Videojuegos;
$collection = $db->JuegosCompletos;

header('Content-Type: application/json'); // Indispensable

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Captura de datos del JSON
$id = $data["_id"] ?? null;
$titulo = $data["titulo"] ?? null;
$precio = $data["precio_base"] ?? null;
$fecha = $data["fecha_lanzamiento"] ?? null; // Corregido el nombre
$pegi = $data["pegi"] ?? null;
$motor = $data["motor"] ?? null;
$multijugador = (isset($data["es_multijugador"]) && $data["es_multijugador"] == "on") ? 1 : 0;
$desc = $data["descripcion"] ?? null;

$nombre = $data["nombre"] ?? null;
$pais = $data["pais"] ?? null;
$ciudad = $data["ciudad"] ?? null;
$fundado_en = $data["fundado_en"] ?? null;
$web = $data["web"] ?? null;
$telefono = $data["telefono"] ?? null;
$email = $data["email"] ?? null;
$titulo_dlc = $data["titulo_dlc"] ?? null;
$fecha_lanzamiento_dlc = $data["fecha_lanzamiento_dlc"] ?? null;
$precio_dlc = $data["precio_dlc"] ?? null;

$accion = $data["accion"] ?? null;


$documento = [];

switch ($accion) {
    case 'insert':
        
        if ($titulo !== "") {
            $documento["titulo"] = $titulo;
        }

        if ($precio !== "") {
            $documento["precio_base"] = (float)$precio;
        }

        if ($fecha_lanzamiento !== "") {
            $documento["fecha_lanzamiento"] = $fecha_lanzamiento;
        }

        if ($pegi !== "") {
            $documento["pegi"] = (int)$pegi;
        }

        if ($motor !== "") {
            $documento["motor"] = $motor;
        }
        if ($multijugador !== "") {
            $documento["es_multijugador"] = $multijugador;
        }
        if ($desc !== "") {
            $documento["descripcion"] = $desc;
        }
        //Estudio
        if ($nombre !== "") {
            $documento["nombre"] = $nombre;
        }
        if ($pais !== "") {
            $documento["pais"] = $pais;
        }
        if ($ciudad !== "") {
            $documento["ciudad"] = $ciudad;
        }
        if ($fundado_en !== "") {
            $documento["fundado_en"] = $fundado_en;
        }
        if ($web !== "") {
            $documento["web"] = $web;
        }
        if ($telefono !== "") {
            $documento["telefono"] = $telefono;
        }
        if ($email !== "") {
            $documento["email"] = $email;
        }
        if ($titulo_dlc !== "") {
            $documento["titulo_dlc"] = $titulo_dlc;
        }
        if ($desc !== "") {
            $documento["descripcion"] = $desc;
        }
        if ($desc !== "") {
            $documento["descripcion"] = $desc;
        }

        if (!empty($documento)) {
            $resultado = $collection->insertOne($documento);
            $mensaje = "Documento insertado con ID: " . $resultado->getInsertedId();
        } else {
            $mensaje = "No se ha insertado nada porque todos los campos estaban vacíos.";
        }

        break;
    case 'update':
        # code...
        break;
    case 'delete':
        # code...
        break;
    case 'find':
        # code...
        break;
    case 'mostrar_todo':
        # code...
        try {
        
            // Obtenemos los juegos y los pasamos a un array
            $cursor = $collection->find();
            $resultados = iterator_to_array($cursor);
        
            // ENVIAR SOLO ESTO
            echo json_encode([
                "status" => "success",
                "datos" => $resultados
            ]);
        
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e.getMessage()
            ]);
        }
        break;
    
    default:
        # code...
        break;
}
