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
//Header con el tipo text/xml para que reconozca el formato
header("Content-Type: text/xml; charset=utf-8");

//Funcion de respuesta tipica de SOAP con el fomato escipulado en el wsdl
function responderSOAP($operacion, $resultado) {
    //Se formatea la respuesta 
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
//lo mismo pero cuando da error
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

//Lo mismo pero este da una respuesta distinta 
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
//Miras que tipo de peticion te llega
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderFault("Este endpoint SOAP solo acepta peticiones POST.");
}
// comprebas que el contenido no se encuentra vacio
$xmlRecibido = file_get_contents("php://input");

if (trim($xmlRecibido) === "") {
    responderFault("No se recibió ningún XML.");
}


libxml_use_internal_errors(true);

//Usa la clase DOMDocument para poder trabajar con xml
$dom = new DOMDocument();
//Comprobamos si el formato del XML es valido
if (!$dom->loadXML($xmlRecibido)) {
    responderFault("El XML recibido no es válido.");
}

// lo mismo para navegar por el xml
$xpath = new DOMXPath($dom);
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

// se compruba que exista una etiqueta Body
$body = $xpath->query("//soap:Body")->item(0);

if (!$body) {
    responderFault("No se encontró el elemento Body.");
}

// Buscar la primera operación dentro del Body
$operacionNode = null;

foreach ($body->childNodes as $nodo) {
    //si el nodo es una intacia de Dom element
    if ($nodo instanceof DOMElement) {
        $operacionNode = $nodo;
        break;
    }
}
//si no se ha encontrado ningun nodo salta este mensaje
if (!$operacionNode) {
    responderFault("No se encontró ninguna operación dentro del Body.");
}

// gurda en una variable el nombre de la operacion
$operacion = $operacionNode->localName;
// lo mismo para el valor de las variable a y b para lo operacion matematica
$aNode = $operacionNode->getElementsByTagName("a")->item(0);
$bNode = $operacionNode->getElementsByTagName("b")->item(0);

//salta el error si falta alguna variable
if (!$aNode || !$bNode) {
    responderFault("Faltan los parámetros a o b.");
}

//extraemos el contenido del nodo del nodo ensi
$a = $aNode->textContent;
$b = $bNode->textContent;
// te aseguras que sean numericos
if (!is_numeric($a) || !is_numeric($b)) {
    responderFault("Los parámetros deben ser numéricos.");
}
// lo pasa a decimales
$a = (float)$a;
$b = (float)$b;

// en funcion de la operacion haces un cosa  otra y devuelves el resutado final mediante la funcion creada anteriomente
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