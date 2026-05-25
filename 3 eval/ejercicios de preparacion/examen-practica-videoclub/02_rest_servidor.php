<?php
/*
 * PARTE 2 — API REST: Videoclub
 * Tiempo estimado: 35 minutos
 *
 * RUTAS:
 * GET    /02_rest_servidor.php/peliculas         → 200, todas
 * GET    /02_rest_servidor.php/peliculas/{id}    → 200 | 404
 * POST   /02_rest_servidor.php/peliculas         → 201 | 400
 * PUT    /02_rest_servidor.php/peliculas/{id}    → 200 | 400 | 404
 * DELETE /02_rest_servidor.php/peliculas/{id}    → 204 | 404
 *
 * CAMPOS OBLIGATORIOS en POST y PUT: titulo, director, genero, precio_alquiler, copias
 * DATOS: peliculas.json en este mismo directorio
 */
header("Content-Type: application/json; charset=utf-8");

$archivo = __DIR__ . "/peliculas.json";

if (!file_exists($archivo)) {
    file_put_contents($archivo, json_encode([
        ["id"=>1,"titulo"=>"Interstellar","director"=>"Christopher Nolan",    "genero"=>"Ciencia Ficción", "precio_alquiler"=>3.50,"copias"=>5],
        ["id"=>2,"titulo"=>"El Padrino",    "director"=>"Francis Ford Coppola", "genero"=>"Drama",           "precio_alquiler"=>2.99,"copias"=>3],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// TODO 1: Implementa leer($archivo)
function leer($archivo) {
    if (!file_exists($archivo)) return [];
    return json_decode(file_get_contents($archivo), true) ?: [];
}

// TODO 2: Implementa guardar($archivo, $datos)
function guardar($archivo, $datos) {
    file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// TODO 3: Implementa responder($codigo, $datos = null)
function responder($codigo, $datos = null) {
    http_response_code($codigo);
    if ($datos !== null) {
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ENRUTADOR BASADO EN PATH_INFO
$metodo = $_SERVER["REQUEST_METHOD"];
$path   = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";
$partes = explode("/", $path);

$recurso = $partes[0] ?? "";
$id      = (isset($partes[1]) && $partes[1] !== "") ? (int)$partes[1] : null;

if ($recurso !== "peliculas") {
    responder(404, ["error" => "Recurso no encontrado. Usa /peliculas"]);
}

$peliculas = leer($archivo);

// ─────────────────────────────────────────────────────────────────────────────
switch ($metodo) {

    // TODO 4 y 5: GET /peliculas y GET /peliculas/{id}
    case "GET":
        if ($id === null) {
            responder(200, $peliculas);
        } else {
            foreach ($peliculas as $p) {
                if ($p["id"] === $id) {
                    responder(200, $p);
                }
            }
            responder(404, ["error" => "Película con ID $id no encontrada"]);
        }
        break;

    // TODO 6: POST /peliculas — Crea una película nueva
    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        
        // Validación estricta de presencia de campos obligatorios
        if (empty($body["titulo"]) || empty($body["director"]) || empty($body["genero"]) || 
            !isset($body["precio_alquiler"]) || !isset($body["copias"])) {
            responder(400, ["error" => "Faltan campos obligatorios: titulo, director, genero, precio_alquiler, copias"]);
        }
        
        // Autoincremento manual obteniendo el id máximo del array
        $ids = array_column($peliculas, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;
        
        $nuevaPelicula = [
            "id"              => $nuevoId,
            "titulo"          => $body["titulo"],
            "director"        => $body["director"],
            "genero"          => $body["genero"],
            "precio_alquiler" => (float)$body["precio_alquiler"],
            "copias"          => (int)$body["copias"]
        ];
        
        $peliculas[] = $nuevaPelicula;
        guardar($archivo, $peliculas);
        responder(201, $nuevaPelicula);
        break;

    // TODO 7: PUT /peliculas/{id} — Actualización completa
    case "PUT":
        if ($id === null) {
            responder(400, ["error" => "Se requiere especificar el ID en la URL"]);
        }
        
        $body = json_decode(file_get_contents("php://input"), true);
        if (empty($body["titulo"]) || empty($body["director"]) || empty($body["genero"]) || 
            !isset($body["precio_alquiler"]) || !isset($body["copias"])) {
            responder(400, ["error" => "Faltan campos obligatorios para actualizar"]);
        }
        
        $indice = null;
        foreach ($peliculas as $i => $p) {
            if ($p["id"] === $id) {
                $indice = $i;
                break;
            }
        }
        
        if ($indice === null) {
            responder(404, ["error" => "Película con ID $id no encontrada"]);
        }
        
        // Reemplazamos el recurso conservando el ID original de la URL
        $peliculas[$indice] = [
            "id"              => $id,
            "titulo"          => $body["titulo"],
            "director"        => $body["director"],
            "genero"          => $body["genero"],
            "precio_alquiler" => (float)$body["precio_alquiler"],
            "copias"          => (int)$body["copias"]
        ];
        
        guardar($archivo, $peliculas);
        responder(200, $peliculas[$indice]);
        break;

    // TODO 8: DELETE /peliculas/{id} — Elimina una película
    case "DELETE":
        if ($id === null) {
            responder(400, ["error" => "Se requiere especificar el ID en la URL"]);
        }
        
        $indice = null;
        foreach ($peliculas as $i => $p) {
            if ($p["id"] === $id) {
                $indice = $i;
                break;
            }
        }
        
        if ($indice === null) {
            responder(404, ["error" => "Película con ID $id no encontrada"]);
        }
        
        array_splice($peliculas, $indice, 1);
        guardar($archivo, $peliculas);
        
        // Código 204 obliga a enviar una respuesta sin cuerpo
        responder(204);
        break;

    default:
        header("Allow: GET, POST, PUT, DELETE");
        responder(405, ["error" => "Método $metodo no permitido"]);
}
?>