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
// TODO 2: implementa guardarNotas($archivo, $a) → guarda array como JSON
// TODO 3: implementa responder($codigo, $datos)  → http_response_code + json_encode + exit
// TODO 4: implementa leerBody()                  → json_decode(php://input) + 400 si inválido
function leerNotas($archivo)            { return []; }   // TODO: json_decode(file_get_contents($archivo), true)
function guardarNotas($archivo, $notas) { }              // TODO: file_put_contents + json_encode
function responder($codigo, $datos = null) { exit; }    // TODO: http_response_code + json_encode + exit
function leerBody()                     { return []; }   // TODO: json_decode(file_get_contents("php://input"))

// TODO 5: lee $metodo, $ruta, $recurso, $id (desde PATH_INFO)
//         si $recurso !== "notas" → responder(404, error)

$notas = leerNotas($archivoDatos);

// TODO 6: GET /notas
// TODO 7: GET /notas/{id}
// TODO 8: POST /notas  — valida titulo y materia, genera id, fecha_creacion=date("Y-m-d")
// TODO 9: PATCH /notas/{id} — actualiza solo titulo, contenido, materia si vienen
// TODO 10: DELETE /notas/{id} — array_splice + guardar + responder(204)
// TODO 11: responder(405, ...) si ningún caso coincidió
?>
