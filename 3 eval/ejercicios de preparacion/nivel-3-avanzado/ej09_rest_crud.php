<?php
/*
 * EJ09 — API REST CRUD completa
 * Recurso: notas de estudio
 * Campos: id, titulo, contenido, materia, fecha_creacion
 * Almacenamiento: notas.json (en este mismo directorio)
 *
 * RUTAS:
 *   GET    /notas        → todas las notas (200)
 *   GET    /notas/{id}   → una nota       (200 / 404)
 *   POST   /notas        → crear          (201 / 400) — requiere titulo y materia
 *   PATCH  /notas/{id}   → actualizar     (200 / 404) — solo los campos que vengan
 *   DELETE /notas/{id}   → eliminar       (204 / 404)
 */
header("Content-Type: application/json; charset=utf-8");

$archivoDatos = __DIR__ . "/notas.json";

if (!file_exists($archivoDatos)) {
    file_put_contents($archivoDatos, json_encode([
        ["id"=>1,"titulo"=>"Status codes","contenido"=>"200 GET, 201 POST, 204 DELETE, 404 Not Found","materia"=>"Servidor","fecha_creacion"=>"2025-01-10"],
        ["id"=>2,"titulo"=>"SOAP Fault","contenido"=>"Para errores de protocolo, no para resultados negativos de negocio","materia"=>"Servidor","fecha_creacion"=>"2025-01-11"],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// TODO 1: implementa leerNotas($archivo)        → devuelve array PHP
function leerNotas($archivo){
    if(!file_exists($archivo))return []; 
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true) ?: [];
     }   // TODO: json_decode(file_get_contents($archivo), true)
// TODO 2: implementa guardarNotas($archivo, $a) → guarda array como JSON
function guardarNotas($archivo, $notas) { 
    file_put_contents($archivo, json_encode($notas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}              // TODO: file_put_contents + json_encode
// TODO 3: implementa responder($codigo, $datos)  → http_response_code + json_encode + exit
function responder($codigo, $datos = null) { 
    http_response_code($codigo);
    if($datos !== null){
        echo json_encode($datos);
    }
    exit; 
    }    // TODO: http_response_code + json_encode + exit
// TODO 4: implementa leerBody()                  → json_decode(php://input) + 400 si inválido
function leerBody() { 
    $inputRaw = file_get_contents("php://input");
    if (empty(trim($inputRaw))) {
        responder(400, ["error" => "El cuerpo de la petición está vacío"]);
    }
    
    $datos = json_decode($inputRaw, true);
    if ($datos === null) {
        responder(400, ["error" => "El JSON enviado no tiene un formato válido"]);
    }
    return $datos; 
}   // TODO: json_decode(file_get_contents("php://input"))

$metodo = $_SERVER["REQUEST_METHOD"];

// PATH_INFO contendrá algo como "/notas" o "/notas/1"
$path = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";
$partes = explode("/", $path);

$recurso = isset($partes[0]) ? $partes[0] : "";
$id = (isset($partes[1]) && $partes[1] !== "") ? (int)$partes[1] : null;

// Si entran a la raíz sin recurso o a un recurso no soportado
if ($recurso !== "notas") {
    responder(404, ["error" => "Recurso no encontrado. Esta API solo maneja /notas"]);
}

$notas = leerNotas($archivoDatos);

// --- ENRUTADOR CRUD ---
switch ($metodo) {
    
    // TODO 6 y 7: GET /notas y GET /notas/{id}
    case "GET":
        if ($id === null) {
            // GET /notas
            responder(200, $notas);
        } else {
            // GET /notas/{id}
            foreach ($notas as $nota) {
                if ($nota["id"] === $id) {
                    responder(200, $nota);
                }
            }
            responder(404, ["error" => "Nota con ID $id no encontrada"]);
        }
        break;

    // TODO 8: POST /notas — valida titulo y materia, genera id, fecha_creacion=date("Y-m-d")
    case "POST":
        if ($id !== null) {
            responder(400, ["error" => "No se puede enviar un ID en la URL para crear un recurso"]);
        }
        
        $body = leerBody();
        
        if (empty($body["titulo"]) || empty($body["materia"])) {
            responder(400, ["error" => "Los campos 'titulo' y 'materia' son obligatorios"]);
        }
        
        // Autoincrementar ID buscando el id más alto actual
        $nuevoId = 1;
        if (!empty($notas)) {
            $ids = array_column($notas, "id");
            $nuevoId = max($ids) + 1;
        }
        
        $nuevaNota = [
            "id" => $nuevoId,
            "titulo" => $body["titulo"],
            "contenido" => isset($body["contenido"]) ? $body["contenido"] : "",
            "materia" => $body["materia"],
            "fecha_creacion" => date("Y-m-d")
        ];
        
        $notas[] = nuevaNota;
        guardarNotas($archivoDatos, $notas);
        responder(201, $nuevaNota);
        break;

    // TODO 9: PATCH /notas/{id} — actualiza solo titulo, contenido, materia si vienen
    case "PATCH":
        if ($id === null) {
            responder(400, ["error" => "Se requiere especificar el ID de la nota en la URL"]);
        }
        
        $body = leerBody();
        $encontrado = false;
        
        foreach ($notas as &$nota) {
            if ($nota["id"] === $id) {
                $encontrado = true;
                
                // Modificamos selectivamente si la clave existe en el cuerpo recibido
                if (array_key_exists("titulo", $body))    $nota["titulo"] = $body["titulo"];
                if (array_key_exists("contenido", $body)) $nota["contenido"] = $body["contenido"];
                if (array_key_exists("materia", $body))   $nota["materia"] = $body["materia"];
                
                break;
            }
        }
        
        if (!$encontrado) {
            responder(404, ["error" => "Nota con ID $id no encontrada"]);
        }
        
        guardarNotas($archivoDatos, $notas);
        // Devolvemos la nota modificada. Como usamos la referencia `&$nota`, buscamos el elemento actualizado.
        $notaActualizada = array_values(array_filter($notas, function($n) use ($id) { return $n["id"] === $id; }))[0];
        responder(200, $notaActualizada);
        break;

    // TODO 10: DELETE /notas/{id} — array_splice + guardar + responder(204)
    case "DELETE":
        if ($id === null) {
            responder(400, ["error" => "Se requiere especificar el ID de la nota en la URL"]);
        }
        
        $indiceEncontrado = null;
        foreach ($notas as $indice => $nota) {
            if ($nota["id"] === $id) {
                $indiceEncontrado = $indice;
                break;
            }
        }
        
        if ($indiceEncontrado === null) {
            responder(404, ["error" => "Nota con ID $id no encontrada"]);
        }
        
        // Eliminamos el elemento usando el índice encontrado
        array_splice($notas, $indiceEncontrado, 1);
        guardarNotas($archivoDatos, $notas);
        
        // Código 204 significa "No Content", por lo que no se envía cuerpo de respuesta
        responder(204);
        break;

    // TODO 11: responder(405, ...) si ningún caso coincidió
    default:
        header("Allow: GET, POST, PATCH, DELETE");
        responder(405, ["error" => "Método $metodo no permitido"]);
        break;
}
?>
