<?php
/*
 * PARTE 2 — API REST: Tienda de Productos
 * Tiempo estimado: 35 minutos
 *
 * Lee la especificación en 03_rest_spec.yaml antes de empezar.
 *
 * RUTAS:
 *   GET    /04_rest_servidor.php/productos        → 200, todos
 *   GET    /04_rest_servidor.php/productos/{id}   → 200 | 404
 *   POST   /04_rest_servidor.php/productos        → 201 | 400
 *   PUT    /04_rest_servidor.php/productos/{id}   → 200 | 400 | 404
 *   DELETE /04_rest_servidor.php/productos/{id}   → 204 | 404
 *
 * CAMPOS OBLIGATORIOS en POST y PUT: nombre, categoria, precio, stock
 * DATOS: productos.json en este mismo directorio
 */
header("Content-Type: application/json; charset=utf-8");

$archivo = __DIR__ . "/productos.json";

// Datos de ejemplo si aún no existe el JSON
if (!file_exists($archivo)) {
    file_put_contents($archivo, json_encode([
        ["id"=>1,"nombre"=>"Camiseta","categoria"=>"Ropa",     "precio"=>19.99,"stock"=>50],
        ["id"=>2,"nombre"=>"Zapatos", "categoria"=>"Calzado",  "precio"=>49.99,"stock"=>20],
        ["id"=>3,"nombre"=>"Libro PHP","categoria"=>"Libros",  "precio"=>35.00,"stock"=>100],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ─────────────────────────────────────────────────────────────────────────────
// TODO 1: Implementa leer($archivo)
//   → Devuelve el array de productos del JSON
//   Pista: return json_decode(file_get_contents($archivo), true) ?: [];
// ─────────────────────────────────────────────────────────────────────────────
function leer($archivo) {
    // TODO 1
    return json_decode(file_get_contents($archivo), true) ?: [];
}

// ─────────────────────────────────────────────────────────────────────────────
// TODO 2: Implementa guardar($archivo, $datos)
//   → Guarda el array como JSON en el archivo
//   Pista: file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
// ─────────────────────────────────────────────────────────────────────────────
function guardar($archivo, $datos) {
    // TODO 2
    file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ─────────────────────────────────────────────────────────────────────────────
// TODO 3: Implementa responder($codigo, $datos = null)
//   → Establece el código HTTP, devuelve JSON si hay datos, y hace exit
//   Pista:
//     http_response_code($codigo);
//     if ($datos !== null) echo json_encode($datos, JSON_UNESCAPED_UNICODE);
//     exit;
// ─────────────────────────────────────────────────────────────────────────────
function responder($codigo, $datos = null) {
    // TODO 3
    http_response_code($codigo);
    if ($datos !== null) {
        echo json_encode(
            $datos ,
             JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
             );
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// ENRUTADOR — Lee el método HTTP y la URL (no modificar)
// ─────────────────────────────────────────────────────────────────────────────
$metodo = $_SERVER["REQUEST_METHOD"];
$path   = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";
$partes = explode("/", $path);
// $partes[0] = "productos"
// $partes[1] = id si existe (ej: "2")

$recurso = $partes[0] ?? "";
$id      = (isset($partes[1]) && $partes[1] !== "") ? (int)$partes[1] : null;

if ($recurso !== "productos") {
    responder(404, ["error" => "Recurso no encontrado. Solo existe /productos"]);
}

$productos = leer($archivo);

// ─────────────────────────────────────────────────────────────────────────────
switch ($metodo) {

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 4: GET /productos — devuelve todos
    //   responder(200, $productos)
    //
    // TODO 5: GET /productos/{id} — busca por id
    //   Recorre $productos con foreach buscando $producto["id"] === $id
    //   Si lo encuentra → responder(200, $producto)
    //   Si no → responder(404, ["error" => "Producto con ID $id no encontrado"])
    // ─────────────────────────────────────────────────────────────────────────
    case "GET":
        if ($id === null) {
            // TODO 4
             responder(200, $productos);
        } else {
            // TODO 5
            foreach ($productos as $p) {
                if ($p["id"] === $id) {
                    responder(200, $p);
                }
            }
            responder(404, ["error" => "Producto con ID $id no encontrado"]);
        }
        break;

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 6: POST /productos — crea un producto nuevo
    //
    // Pasos:
    //   f) guardar($archivo, $productos);
    //   g) responder(201, $nuevo);
    // ─────────────────────────────────────────────────────────────────────────
    case "POST":
        // TODO 6
        //   a) Lee el body JSON: $body = json_decode(file_get_contents("php://input"), true);
        $body = json_decode(file_get_contents("php://input"), true);
        
        //   b) Valida que existan los 4 campos: nombre, categoria, precio, stock
        //      Si falta alguno → responder(400, ["error" => "Faltan campos: nombre, categoria, precio, stock"])
        if (empty($body["nombre"]) || empty($body["categoria"]) || !isset($body["precio"]) || !isset($body["stock"])) {
            responder(400, ["error" => "Faltan campos obligatorios: nombre, categoria, precio, stock"]);
            }
        //   c) Genera el nuevo id:
        //      $ids = array_column($productos, "id");
        //      $nuevoId = empty($ids) ? 1 : max($ids) + 1;
        $ids = array_column($productos,"id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;
        //   d) Crea el nuevo producto como array asociativo con los 5 campos (id + los 4 del body)
        $nuevo = [
            "id"        => $nuevoId,
            "nombre"    => $body["nombre"],
            "categoria" => $body["categoria"],
            "precio"    => (float)$body["precio"],
            "stock"     => (int)$body["stock"]
            ]; 
            
        //   e) Añade al array: $productos[] = $nuevo;
        $productos[] = $nuevo;

        //   f) guardar($archivo, $productos);
        guardar($archivo, $productos);
        //   g) responder(201, $nuevo);
        responder(201, $nuevo);
        break;

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 7: PUT /productos/{id} — actualización completa
    //
    // Pasos:
    
    //   e) Reemplaza TODOS los campos pero conserva el id original:
    //      $productos[$indice] = ["id" => $id, "nombre" => ..., "categoria" => ..., "precio" => ..., "stock" => ...];
    //   f) guardar + responder(200, $productos[$indice])
    // ─────────────────────────────────────────────────────────────────────────
    case "PUT":
        // TODO 7
        //   a) Si no hay $id → responder(400, ["error" => "Se requiere ID en la URL"])
        if($id === null){
            responder(400,["error" => "se requiere ID en la URL"]);
            }
            
        //   b) Lee body JSON y valida los 4 campos (igual que POST)
        $body = json_decode(file_get_contents("php://input"), true);
        if (empty($body["nombre"]) || empty($body["categoria"]) || !isset($body["precio"]) || !isset($body["stock"])) {
            responder(400, ["error" => "Faltan campos obligatorios: nombre, categoria, precio, stock"]);
            }
            
        //   c) Busca el índice del producto con ese id:
        //      $indice = null;
        //      foreach ($productos as $i => $p) { if ($p["id"] === $id) { $indice = $i; break; } }
        $indice = "";
        foreach ($productos as $i => $p){
            if ($p["id"] === $id) {
                $indice = $i;
                break;
            }
        };
        //   d) Si no existe → responder(404, ...)
        if ($indice === null) {
            responder(404, ["error" => "Producto con ID $id no encontrado"]);
        }

        //   e) Reemplaza TODOS los campos pero conserva el id original:
    //      $productos[$indice] = ["id" => $id, "nombre" => ..., "categoria" => ..., "precio" => ..., "stock" => ...];
        $productos[$indice] = [
            "id"        => $id,
            "nombre"    => $body["nombre"],
            "categoria" => $body["categoria"],
            "precio"    => (float)$body["precio"],
            "stock"     => (int)$body["stock"]
        ];


        guardar($archivo, $productos);
        responder(200, $productos[$indice]);

        break;

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 8: DELETE /productos/{id} — elimina un producto
    //
    // Pasos:
    //   a) Si no hay $id → responder(400, ["error" => "Se requiere ID en la URL"])
    //   b) Busca el índice igual que en PUT
    //   c) Si no existe → responder(404, ...)
    //   d) Elimina con: array_splice($productos, $indice, 1);
    //   e) guardar($archivo, $productos);
    //   f) responder(204);   ← 204 No Content, sin body
    // ─────────────────────────────────────────────────────────────────────────
    case "DELETE":
        // TODO 8
        if ($id === null) {
            responder(400, ["error" => "Se requiere ID en la URL"]);
        }
        
        $indice = null;
        foreach ($productos as $i => $p) {
            if ($p["id"] === $id) {
                $indice = $i;
                break;
            }
        }
        
        if ($indice === null) {
            responder(404, ["error" => "Producto con ID $id no encontrado"]);
        }
        
        array_splice($productos, $indice, 1);
        guardar($archivo, $productos);
        responder(204); // Sin cuerpo
        break;

    default:
        header("Allow: GET, POST, PUT, DELETE");
        responder(405, ["error" => "Método $metodo no permitido"]);
}
?>

<!--
═══════════════════════════════════════════════════════════════════════════════
  SOLUCIÓN COMPLETA — Mírala solo si ya lo has intentado
═══════════════════════════════════════════════════════════════════════════════

TODO 1:
    function leer($archivo) {
        return json_decode(file_get_contents($archivo), true) ?: [];
    }

TODO 2:
    function guardar($archivo, $datos) {
        file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

TODO 3:
    function responder($codigo, $datos = null) {
        http_response_code($codigo);
        if ($datos !== null) echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

TODO 4 y 5:
    case "GET":
        if ($id === null) {
            responder(200, $productos);
        } else {
            foreach ($productos as $p) {
                if ($p["id"] === $id) responder(200, $p);
            }
            responder(404, ["error" => "Producto con ID $id no encontrado"]);
        }
        break;

TODO 6:
    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        if (empty($body["nombre"]) || empty($body["categoria"]) ||
            !isset($body["precio"]) || !isset($body["stock"])) {
            responder(400, ["error" => "Faltan campos obligatorios: nombre, categoria, precio, stock"]);
        }
        $ids = array_column($productos, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;
        $nuevo = [
            "id"        => $nuevoId,
            "nombre"    => $body["nombre"],
            "categoria" => $body["categoria"],
            "precio"    => (float)$body["precio"],
            "stock"     => (int)$body["stock"]
        ];
        $productos[] = $nuevo;
        guardar($archivo, $productos);
        responder(201, $nuevo);
        break;

TODO 7:
    case "PUT":
        if ($id === null) responder(400, ["error" => "Se requiere ID en la URL"]);
        $body = json_decode(file_get_contents("php://input"), true);
        if (empty($body["nombre"]) || empty($body["categoria"]) ||
            !isset($body["precio"]) || !isset($body["stock"])) {
            responder(400, ["error" => "Faltan campos obligatorios: nombre, categoria, precio, stock"]);
        }
        $indice = null;
        foreach ($productos as $i => $p) {
            if ($p["id"] === $id) { $indice = $i; break; }
        }
        if ($indice === null) responder(404, ["error" => "Producto con ID $id no encontrado"]);
        $productos[$indice] = [
            "id"        => $id,
            "nombre"    => $body["nombre"],
            "categoria" => $body["categoria"],
            "precio"    => (float)$body["precio"],
            "stock"     => (int)$body["stock"]
        ];
        guardar($archivo, $productos);
        responder(200, $productos[$indice]);
        break;

TODO 8:
    case "DELETE":
        if ($id === null) responder(400, ["error" => "Se requiere ID en la URL"]);
        $indice = null;
        foreach ($productos as $i => $p) {
            if ($p["id"] === $id) { $indice = $i; break; }
        }
        if ($indice === null) responder(404, ["error" => "Producto con ID $id no encontrado"]);
        array_splice($productos, $indice, 1);
        guardar($archivo, $productos);
        responder(204);
        break;

═══════════════════════════════════════════════════════════════════════════════
-->
