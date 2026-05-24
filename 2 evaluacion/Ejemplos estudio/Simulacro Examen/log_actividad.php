<?php
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

try {
    
    $client = new Client("mongodb://localhost:27017");
    $coleccion = $client->Gaming->Logs;
} catch (Exception $e) {
    die("Error al conectar a MongoDB:" . $e->getMessage());
}

// TODO 9: Crear una función que inserte un LOG con esta estructura:
/*
{
  "usuario_id": "valor",
  "operacion": "canje_skin",
  "detalles": {
     "skin_id": "valor",
     "fecha": "2024-..."
  },
  "etiquetas": ["recompensa", "tienda"]
}
*/
function insertarLog($coleccion, $u, $s) {
    // Tu código aquí...
    $documento = [
        "usuario_id"=> $u,
        "operacion"=> "canje_skin",
        "detalles"=>[
            "skin_id"=> $s,
            "fecha"=> "2024-..."
        ],
        "etiquetas"=> ["recompensa", "tienda"]

    ];
    $coleccion->insertOne($documento);
}

// TODO 10: Implementar búsqueda en MongoDB.
// Buscar todos los documentos donde "usuario_id" sea igual al recibido.
// Devolver un array con los resultados para ser enviado como JSON.

if ($_GET['accion'] == "buscar") {
    $idABuscar = $_GET['id'];
    // Tu código aquí (Recuerda el foreach para el cursor)...
    $cursores = $coleccion->find(["usuario_id" => $idABuscar]);

    $resultado = [];

    foreach($cursores as $cursor){
 
        $resultado[] = [
            "id_mongo" =>(string)$cursor["_id"],
            "operacion"  => $cursor["operacion"],
            "skin_id"    => $cursor["detalles"]["skin_id"],
            "fecha"      => $cursor["detalles"]["fecha"]
        ];
    }

    header("Content-Type: application/json");
    echo json_encode([
        "ok" => true, 
        "total" => count($resultados), 
        "logs" => $resultados
    ]);

}