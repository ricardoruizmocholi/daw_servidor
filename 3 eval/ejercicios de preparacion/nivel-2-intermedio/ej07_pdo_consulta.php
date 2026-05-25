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
}

// TODO 2: Lee $_GET["precio_max"] — si existe y es numérico, úsalo como filtro
//         Si no existe, $precioMax = null

// TODO 3: Dentro de try-catch(PDOException):
//
//   a) $pdo = obtenerPDO()
//
//   b) Si $precioMax !== null:
//      - prepare("SELECT id_producto, nombre, precio FROM producto WHERE precio <= :max ORDER BY precio")
//      - bindParam(":max", $precioMax)
//      - execute()
//
//   c) Si $precioMax === null:
//      - query("SELECT id_producto, nombre, precio FROM producto ORDER BY nombre")
//
//   d) fetchAll(PDO::FETCH_ASSOC)
//   e) http_response_code(200) + json_encode con total, filtro y productos
//
//   En el catch: http_response_code(500) + json_encode con ok:false y mensaje de error

try {

} catch (PDOException $e) {

}
?>
