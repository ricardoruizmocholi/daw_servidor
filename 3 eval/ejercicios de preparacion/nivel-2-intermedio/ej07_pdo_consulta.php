<?php
/*
 * EJ07 — Conexión PDO y consulta preparada
 * Concepto: PDO, prepare(), bindParam(), execute(), fetchAll(), PDOException
 * Base de datos: tienda_servicios (la misma de la carpeta 001)
 * Parámetro GET opcional: ?precio_max=50 → filtra productos por precio
 */

header("Content-Type: application/json; charset=utf-8");

// TODO 1: implementa obtenerPDO()
//   - host: "127.0.0.1", port: "3306", dbname: "tienda_servicios", user: "root", pass: ""
//   - Usa PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
//   - Devuelve el objeto PDO
function obtenerPDO() {
    // TODO: new PDO("mysql:host=...;dbname=...;charset=utf8mb4", $user, $pass)
    //       + setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)
    //       + return $pdo

    $host = "127.0.0.1";
    $port = "3306";
    $dbname = "tienda_servicios";
    $user = "root";
    $pass = "";
    
    // Cadena de conexión (DSN) especificando también el juego de caracteres utf8mb4
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $user, $pass);
    
    // Configuramos para que lance excepciones en caso de errores SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $pdo;
}

// TODO 2: Lee $_GET["precio_max"] — si existe y es numérico, úsalo como filtro
//         Si no existe, $precioMax = null

$precioMax = (isset($_GET["precio_max"]) && is_numeric($_GET["precio_max"]) ? (float)$_GET["precio_max"] : null);

// TODO 3: Dentro de try-catch(PDOException):
//

try {
    
    //   a) $pdo = obtenerPDO()
    //
    $pdo = obtenerPDO();
    //   b) Si $precioMax !== null:
    //      - prepare("SELECT id_producto, nombre, precio FROM producto WHERE precio <= :max ORDER BY precio")
    //      - bindParam(":max", $precioMax)
    //      - execute()
    //
    if ($precioMax !== null) {
        // Al usar filtros externos, preparamos la consulta para evitar Inyección SQL
        $stmt = $pdo->prepare("SELECT id_producto, nombre, precio FROM producto WHERE precio <= :max ORDER BY precio");
        
        // Vinculamos el parámetro indicando explícitamente que es un número decimal (float/input as string/param)
        // PDO::PARAM_STR suele ser el más seguro por defecto para decimales en PDO, o dejar que infiera.
        $stmt->bindParam(":max", $precioMax);
        $stmt->execute();
    } else {
        //   c) Si $precioMax === null:
        //      - query("SELECT id_producto, nombre, precio FROM producto ORDER BY nombre")
        $stmt = $pdo->query("SELECT id_producto, nombre, precio FROM producto ORDER BY nombre");
    }
    //
    //   d) fetchAll(PDO::FETCH_ASSOC)
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //   e) http_response_code(200) + json_encode con total, filtro y productos
    //
    http_response_code(200);
    echo json_encode([
        "ok" => true,
        "total" => count($productos),
        "filtro_precio_max" => $precioMax,
        "productos" => $productos
    ]);
    //   En el catch: http_response_code(500) + json_encode con ok:false y mensaje de error

} catch (PDOException $e) {

    // En el catch: http_response_code(500) + json_encode con ok:false y mensaje de error
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "Error en la base de datos",
        "mensaje" => $e->getMessage() // Nota: En entornos de producción reales es mejor no mostrar el $e->getMessage() crudo por seguridad
    ]);
}
?>
