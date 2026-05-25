<?php
/*
 * PARTE 1 — Servidor SOAP: Tienda de Productos
 * Tiempo estimado: 30 minutos
 *
 * Lee el WSDL (01_productos.wsdl) para entender el contrato del servicio.
 *
 * OPERACIÓN 1: consultarStock
 *   Entrada:  <nombre>Camiseta</nombre>
 *   Salida:   <stock>50</stock><disponible>true</disponible>
 *   Error:    Fault si el producto no existe
 *
 * OPERACIÓN 2: obtenerPrecio
 *   Entrada:  <nombre>Zapatos</nombre>
 *   Salida:   <nombre>Zapatos</nombre><precio>49.99</precio>
 *   Error:    Fault si el producto no existe
 *
 * DATOS: productos.json (en este mismo directorio)
 */

// TODO 1: Pon el header correcto para SOAP
// Pista: header("Content-Type: text/xml; charset=utf-8");
header("Content-Type: text/xml; charset=utf-8");

// ─────────────────────────────────────────────────────────────────────────────
// TODO 2: Implementa responderFault($mensaje)
//
function responderFault ($mensaje){
    http_response_code(500);
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
    echo '  <soap:Body>';
    echo '    <soap:Fault>';
    echo '      <faultcode>SOAP-ENV:Client</faultcode>';
    echo '      <faultstring>' . htmlspecialchars($mensaje, ENT_XML1, 'UTF-8') . '</faultstring>';
    echo '    </soap:Fault>';
    echo '  </soap:Body>';
    echo '</soap:Envelope>';
    exit;
}


// ─────────────────────────────────────────────────────────────────────────────
// TODO 3: Implementa responderSOAP($etiquetaRespuesta, array $campos)
//
// $etiquetaRespuesta: nombre del tag de respuesta, ej: "consultarStockResponse"
// $campos: array asociativo con los datos, ej: ["stock" => 50, "disponible" => "true"]
//
// Debe devolver este XML (ejemplo con consultarStockResponse):
function responderSOAP($etiquetaRespuesta, array $campos) {
    http_response_code(200);
    $contenido = "";
    foreach($campos as $clave => $valor){
        $contenido .= "<$clave>". htmlspecialchars((string)$valor, ENT_XML1, 'UTF-8') . "</$clave>";
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
    echo '  <soap:Body>';
    echo '      <$etiquetaRespuesta>$contenido</$etiquetaRespuesta>"';
    echo '  </soap:Body>';
    echo '</soap:Envelope>';
    exit;

}


// ─────────────────────────────────────────────────────────────────────────────
// BLOQUE ESTÁNDAR — Leer y parsear la petición SOAP entrante
// (Este bloque es igual en todos los ejercicios SOAP — memorizarlo)
// ─────────────────────────────────────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderFault("SOAP solo acepta peticiones POST.");
}

$rawXml = file_get_contents("php://input");
if (trim($rawXml) === "") {
    responderFault("El cuerpo de la petición está vacío.");
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
if (!$dom->loadXML($rawXml)) {
    responderFault("El XML recibido está mal formado.");
}

$xpath = new DOMXPath($dom);
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

// Localiza el Body y extrae el primer elemento hijo (la operación llamada)
$opNodo = null;
$bodyNodo = $xpath->query("//soap:Body")->item(0);
if ($bodyNodo) {
    foreach ($bodyNodo->childNodes as $nodo) {
        if ($nodo instanceof DOMElement) {
            $opNodo = $nodo;
            break;
        }
    }
}
if (!$opNodo) {
    responderFault("No se encontró ninguna operación en el Body SOAP.");
}

// $operacion = "consultarStock" o "obtenerPrecio"
$operacion = $opNodo->localName;

// ─────────────────────────────────────────────────────────────────────────────
// TODO 4: Lee productos.json y decodifícalo en un array PHP
// Pista: $productos = json_decode(file_get_contents(__DIR__ . "/productos.json"), true);
// ─────────────────────────────────────────────────────────────────────────────
$productos = json_decode(file_get_contents(__DIR__ . "/productos.json"), true) ?: [];
// TODO 4


// ─────────────────────────────────────────────────────────────────────────────
// ENRUTADOR DE OPERACIONES
// ─────────────────────────────────────────────────────────────────────────────
switch ($operacion) {

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 5: Implementa "consultarStock"
    //
    // Pasos:
    //   a) Extrae el valor de <nombre> del $opNodo:
    //      $nombre = trim($opNodo->getElementsByTagName("nombre")->item(0)->nodeValue);
    //   b) Si $nombre está vacío → responderFault("Falta el parámetro <nombre>")
    //   c) Busca el producto en $productos comparando en minúsculas:
    //      strtolower($producto["nombre"]) === strtolower($nombre)
    //   d) Si no existe → responderFault("Producto '$nombre' no encontrado")
    //   e) Si existe → responderSOAP("consultarStockResponse", [
    //          "stock"      => $producto["stock"],
    //          "disponible" => ($producto["stock"] > 0) ? "true" : "false"
    //      ])
    // ─────────────────────────────────────────────────────────────────────────
    case "consultarStock":
        $nombre = trim($opNodo->getElementsByTagName("nombre")->item(0)->nodeValue);
        if($nombre === ""){
            responderFault("El parametro <nombre esta vacio.");
        }
        $encontrado = "";
        foreach ($productos as $producto){
            if(strtolower($producto["nombre"]) === strtolower($nombre)){
                $encontrado = $producto;
                break;
            };

        }
        if (!$encontrado){
            responderFault("Procuto '$nombre' no encontrado");
        }
        responderSOAP("consultarStockResponse", [
              "stock"      => $producto["stock"],
              "disponible" => ($producto["stock"] > 0) ? "true" : "false"
          ]);
        // TODO 5
        break;

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 6: Implementa "obtenerPrecio"
    //
    // Igual que consultarStock pero devuelve nombre y precio:
    //   responderSOAP("obtenerPrecioResponse", [
    //       "nombre" => $producto["nombre"],
    //       "precio" => $producto["precio"]
    //   ])
    // ─────────────────────────────────────────────────────────────────────────
    case "obtenerPrecio":
        $nombreNodo = $opNodo->getElementsByTagName("nombre")->item(0);
        if (!$nombreNodo) {
            responderFault("Falta el parámetro <nombre>.");
        }
        
        $nombre = trim($nombreNodo->nodeValue);
        if ($nombre === "") {
            responderFault("El parámetro <nombre> no puede estar vacío.");
        }
        
        $encontrado = null;
        foreach ($productos as $p) {
            if (strtolower($p["nombre"]) === strtolower($nombre)) {
                $encontrado = $p;
                break;
            }
        }
        
        if (!$encontrado) {
            responderFault("Producto '$nombre' no encontrado.");
        }
        
        responderSOAP("obtenerPrecioResponse", [
            "nombre" => $encontrado["nombre"],
            "precio" => $encontrado["precio"]
        ]);
        break;

    default:
        responderFault("Operación '$operacion' no está soportada por este servicio.");
}
?>

<!--
═══════════════════════════════════════════════════════════════════════════════
  SOLUCIÓN COMPLETA — Mírala solo si ya lo has intentado
═══════════════════════════════════════════════════════════════════════════════

TODO 1:
    header("Content-Type: text/xml; charset=utf-8");


TODO 4:
    $productos = json_decode(file_get_contents(__DIR__ . "/productos.json"), true) ?: [];

TODO 5:
    case "consultarStock":
        $nombreNodo = $opNodo->getElementsByTagName("nombre")->item(0);
        if (!$nombreNodo) responderFault("Falta el parámetro <nombre>.");
        $nombre = trim($nombreNodo->nodeValue);
        if ($nombre === "") responderFault("El parámetro <nombre> no puede estar vacío.");
        $encontrado = null;
        foreach ($productos as $p) {
            if (strtolower($p["nombre"]) === strtolower($nombre)) {
                $encontrado = $p;
                break;
            }
        }
        if (!$encontrado) responderFault("Producto '$nombre' no encontrado.");
        responderSOAP("consultarStockResponse", [
            "stock"      => $encontrado["stock"],
            "disponible" => ($encontrado["stock"] > 0) ? "true" : "false"
        ]);
        break;

TODO 6:
    case "obtenerPrecio":
        $nombreNodo = $opNodo->getElementsByTagName("nombre")->item(0);
        if (!$nombreNodo) responderFault("Falta el parámetro <nombre>.");
        $nombre = trim($nombreNodo->nodeValue);
        if ($nombre === "") responderFault("El parámetro <nombre> no puede estar vacío.");
        $encontrado = null;
        foreach ($productos as $p) {
            if (strtolower($p["nombre"]) === strtolower($nombre)) {
                $encontrado = $p;
                break;
            }
        }
        if (!$encontrado) responderFault("Producto '$nombre' no encontrado.");
        responderSOAP("obtenerPrecioResponse", [
            "nombre" => $encontrado["nombre"],
            "precio" => $encontrado["precio"]
        ]);
        break;

═══════════════════════════════════════════════════════════════════════════════
-->
