<?php
/*
 * OBJETIVO DEL EJERCICIO: Ampliar la calculadora SOAP con multiplicación y división
 * CONCEPTO QUE ENTRENA: SOAP Fault para errores de lógica de negocio (división entre 0),
 *                       y cómo devolver MÚLTIPLES valores en una respuesta SOAP.
 * PARA EL EXAMEN: La división necesita una función de respuesta especial porque devuelve
 *                 DOS valores (resultado Y resto), mientras que sumar/restar/multiplicar
 *                 solo devuelven uno. Cada estructura de respuesta diferente necesita
 *                 su propia función de construcción del XML.
 */
header("Content-Type: text/xml; charset=utf-8");

function responderSOAP($operacion, $resultado) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <' . $operacion . 'Response>
            <resultado>' . $resultado . '</resultado>
            </' . $operacion . 'Response>
        </soap:Body>
        </soap:Envelope>';
    exit;
}

function responderFault($mensaje) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <soap:Fault>
            <faultcode>SOAP-ENV:Client</faultcode>
            <faultstring>' . htmlspecialchars($mensaje, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</faultstring>
            </soap:Fault>
        </soap:Body>
        </soap:Envelope>';
    exit;
}

// Esta función se usa SOLO para la división, porque devuelve DOS valores (resultado Y resto).
// No podemos reutilizar responderSOAP() que solo sabe devolver uno.
// Piénsalo así: responderSOAP es como una caja con un cajón; esta función es una caja con dos.
function responderSOAPDivision($resultado, $resto) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <dividirResponse>
            <resultado>' . $resultado . '</resultado>
            <resto>' . $resto . '</resto>
            </dividirResponse>
        </soap:Body>
        </soap:Envelope>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderFault("Este endpoint SOAP solo acepta peticiones POST.");
}

$xmlRecibido = file_get_contents("php://input");

if (trim($xmlRecibido) === "") {
    responderFault("No se recibió ningún XML.");
}

libxml_use_internal_errors(true);

$dom = new DOMDocument();

if (!$dom->loadXML($xmlRecibido)) {
    responderFault("El XML recibido no es válido.");
}

$xpath = new DOMXPath($dom);
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

$body = $xpath->query("//soap:Body")->item(0);

if (!$body) {
    responderFault("No se encontró el elemento Body.");
}

// Buscar la primera operación dentro del Body
$operacionNode = null;

foreach ($body->childNodes as $nodo) {
    if ($nodo instanceof DOMElement) {
        $operacionNode = $nodo;
        break;
    }
}

if (!$operacionNode) {
    responderFault("No se encontró ninguna operación dentro del Body.");
}

$operacion = $operacionNode->localName;

$aNode = $operacionNode->getElementsByTagName("a")->item(0);
$bNode = $operacionNode->getElementsByTagName("b")->item(0);

if (!$aNode || !$bNode) {
    responderFault("Faltan los parámetros a o b.");
}

$a = $aNode->textContent;
$b = $bNode->textContent;

if (!is_numeric($a) || !is_numeric($b)) {
    responderFault("Los parámetros deben ser numéricos.");
}

$a = (float)$a;
$b = (float)$b;

switch ($operacion) {
    case "sumar":
        $resultado = $a + $b;
        responderSOAP("sumar", $resultado);
        break;

    case "restar":
        $resultado = $a - $b;
        responderSOAP("restar", $resultado);
        break;

    // Multiplicar: misma estructura que sumar y restar — un resultado, una función estándar
    case "multiplicar":
        $resultado = $a * $b;
        responderSOAP("multiplicar", $resultado);
        break;

    // Dividir: caso especial — devuelve resultado Y resto, y puede lanzar un Fault
    case "dividir":
        // La división entre 0 es matemáticamente imposible — aquí es donde SOAP Fault brilla:
        // no devolvemos un resultado con error dentro, sino que el propio protocolo señala el fallo.
        if ($b == 0) {
            responderFault("No se puede dividir entre 0.");
        }

        // intdiv() da la parte entera de la división (cuántas veces cabe b en a)
        $resultado = intdiv((int)$a, (int)$b);

        // fmod() da el resto de la división real (como el % pero para floats)
        $resto = fmod($a, $b);

        // Usamos la función especial que construye un XML con dos campos en la respuesta
        responderSOAPDivision($resultado, $resto);
        break;

    default:
        responderFault("La operación '$operacion' no está soportada.");
}
?>