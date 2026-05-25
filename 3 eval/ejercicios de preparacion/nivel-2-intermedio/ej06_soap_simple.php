<?php
/*
 * EJ06 — Servicio SOAP mínimo
 * Operación: saludar(nombre) → "<saludarResponse><saludo>¡Hola, X!</saludo></saludarResponse>"
 * Concepto: DOMDocument, DOMXPath, construcción de XML SOAP
 */

// TODO 1: header Content-Type XML ("text/xml; charset=utf-8")
header("Content-Type: text/xml; charset=utf-8");

// TODO 2: implementa responderSOAP($saludo)
//         Devuelve Envelope > Body > <saludarResponse><saludo>...</saludo></saludarResponse>
function responderSOAP($saludo) {
    http_response_code(200);
    
    // Construimos la estructura de éxito estándar de SOAP
    $xml = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <saludarResponse>
                <saludo>$saludo</saludo>
            </saludarResponse>
        </soap:Body>
    </soap:Envelope>
    XML;

    echo $xml;
    exit;
}

// TODO 3: implementa responderFault($mensaje)
//         Devuelve Envelope > Body > <soap:Fault><faultcode>...<faultstring>...
function responderFault($mensaje) {

    // TODO: echo '<?xml version...' + Envelope + Body + <soap:Fault> + exit
    http_response_code(500);

    $xml = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
            <soap:Fault>
                <faultcode>soap:Client</faultcode>
                <faultstring>$mensaje</faultstring>
            </soap:Fault>
        </soap:Body>
    </soap:Envelope>

    XML;
    
    exit;
}

// TODO 4: valida que el método sea POST (si no → Fault)
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    responderFault("Método HTTP no permitido. SOAP requiere POST.");
}

// TODO 5: lee file_get_contents("php://input"), valida que no esté vacío
//         crea DOMDocument, llama a loadXML(), si falla → Fault
$inputRaw = file_get_contents("php://input");

if (empty(trim($inputRaw))) {
    responderFault("El cuerpo de la petición SOAP está vacío.");
}
// Configuramos errores internos de libxml para capturar fallos de parseo sin lanzar warnings de PHP
libxml_use_internal_errors(true);

$doc = new DOMDocument();
if (!$doc->loadXML($inputRaw)) {
    responderFault("El XML enviado no es válido o está mal estructurado.");
}

// TODO 6: crea DOMXPath, registra namespace "soap",
//         navega al Body, obtén el primer hijo (la operación),
//         extrae el elemento <nombre>

$xpath = new DOMXPath($doc);
// Registramos el prefijo 'soap' asociándolo a su URI estándar (debe coincidir con el del cliente)
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

// Buscamos el nodo <nombre> que esté dentro de la operación en el Body
// Usamos //soap:Body/*[1]/nombre para ir al primer hijo del Body (la operación) y buscar <nombre> dentro
$nodosNombre = $xpath->query("//soap:Body/*[1]/nombre");

// Validamos si la consulta XPath devolvió algún nodo válido
if ($nodosNombre->length === 0) {
    responderFault("No se encontró el parámetro requerido <nombre> en la petición.");
}

// Extraemos el valor de texto del nodo encontrado
$nombre = trim($nodosNombre->item(0)->nodeValue);
// TODO 7: valida que $nombre no esté vacío → Fault si lo está
//         llama a responderSOAP("¡Hola, $nombre!")

if ($nombre === "") {
    responderFault("El parámetro <nombre> no puede estar vacío.");
}

responderSOAP("¡Hola, $nombre!");
?>
