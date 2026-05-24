<?php
/*
 * OBJETIVO DEL EJERCICIO: Implementar el servicio SOAP de préstamo de biblioteca
 *                         a partir del contrato WSDL ya diseñado.
 * CONCEPTO QUE ENTRENA: Contract-first (WSDL primero, implementación después)
 *                       y SOAP Headers para transportar tokens de sesión.
 * PARA EL EXAMEN: El Header se procesa ANTES que el Body. Si el token no es válido,
 *                 respondemos con Fault y paramos — el servidor no procesa la operación
 *                 si el "portero" (la sesión) no da el visto bueno.
 */
header("Content-Type: text/xml; charset=utf-8");

// ─────────────────────────────────────────────────────────────────────────────
// Funciones de respuesta XML
// ─────────────────────────────────────────────────────────────────────────────

// Construye la respuesta exitosa de consultarPrestamo.
// El WSDL define que la respuesta tiene tres campos: puede_prestar, mensaje, dias_maximos.
function responderSOAPPrestamo($puedePRestar, $mensaje, $diasMaximos) {
    // En XML los booleanos son "true"/"false" en minúsculas — no 1/0 ni "Sí"/"No"
    $booleanoXML = $puedePRestar ? "true" : "false";

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <consultarPrestamoResponse>
            <puede_prestar>' . $booleanoXML . '</puede_prestar>
            <mensaje>' . htmlspecialchars($mensaje, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</mensaje>
            <dias_maximos>' . (int)$diasMaximos . '</dias_maximos>
            </consultarPrestamoResponse>
        </soap:Body>
        </soap:Envelope>';
    exit;
}

// Construye un SOAP Fault — la única forma correcta de señalar errores en SOAP.
// Un Fault siempre interrumpe el flujo: el cliente sabe que algo fue mal.
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

// ─────────────────────────────────────────────────────────────────────────────
// Lógica simulada de la biblioteca
// ─────────────────────────────────────────────────────────────────────────────

// En un sistema real esto vendría de la base de datos.
// Aquí lo simulamos con arrays estáticos para centrarnos en el protocolo SOAP.

// Tokens de sesión válidos — en producción estarían en la BD o en caché (Redis, etc.)
$tokensValidos = ["ABC-123", "XYZ-789", "CEAC-2025"];

// Libros que están dados de baja o deteriorados — no se pueden prestar
$librosNoDisponibles = ["BAJA-001", "ROTO-042", "PERDIDO-007"];

// ─────────────────────────────────────────────────────────────────────────────
// Validación del método HTTP
// ─────────────────────────────────────────────────────────────────────────────

// SOAP solo funciona con POST — GET no tiene Body, y el XML de la petición va en el Body
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderFault("Este servicio SOAP solo acepta peticiones POST.");
}

// ─────────────────────────────────────────────────────────────────────────────
// Lectura y parseo del XML
// ─────────────────────────────────────────────────────────────────────────────

$xmlRecibido = file_get_contents("php://input");

if (trim($xmlRecibido) === "") {
    responderFault("No se recibió ningún XML en el cuerpo de la petición.");
}

// Activamos el manejo interno de errores XML para poder capturarlos nosotros
libxml_use_internal_errors(true);

$dom = new DOMDocument();
if (!$dom->loadXML($xmlRecibido)) {
    responderFault("El XML recibido no es válido.");
}

$xpath = new DOMXPath($dom);
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

// ─────────────────────────────────────────────────────────────────────────────
// PASO 1: Leer y validar el Header de sesión
// Esto va ANTES que el Body — si la sesión no es válida, no procesamos nada.
// Es como el portero de una discoteca: primero comprueba si entras, luego preguntas qué quieres.
// ─────────────────────────────────────────────────────────────────────────────

$header = $xpath->query("//soap:Header")->item(0);

if (!($header instanceof DOMElement)) {
    responderFault("Se requiere un Header SOAP con el token de sesión.");
}

// Buscamos el nodo <token> dentro del Header
$tokenNode = $header->getElementsByTagName("token")->item(0);

if (!$tokenNode) {
    responderFault("El Header no contiene el elemento <token>.");
}

$token = trim($tokenNode->textContent);

if ($token === "") {
    responderFault("El token de sesión no puede estar vacío.");
}

// Comprobamos si el token está en la lista de tokens válidos
if (!in_array($token, $tokensValidos, true)) {
    responderFault("Token de sesión no válido o expirado: '$token'.");
}

// ─────────────────────────────────────────────────────────────────────────────
// PASO 2: Leer el Body y la operación
// Solo llegamos aquí si el token era válido — el portero nos dejó pasar.
// ─────────────────────────────────────────────────────────────────────────────

$body = $xpath->query("//soap:Body")->item(0);

if (!$body) {
    responderFault("No se encontró el elemento Body en el Envelope.");
}

// Buscamos el primer elemento hijo del Body — ese es el nombre de la operación
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

// Este servicio solo implementa una operación: consultarPrestamo (según el WSDL)
if ($operacion !== "consultarPrestamo") {
    responderFault("La operación '$operacion' no está definida en este servicio.");
}

// ─────────────────────────────────────────────────────────────────────────────
// PASO 3: Extraer y validar los parámetros de la operación
// ─────────────────────────────────────────────────────────────────────────────

$dniNode = $operacionNode->getElementsByTagName("dni")->item(0);
$codigoLibroNode = $operacionNode->getElementsByTagName("codigoLibro")->item(0);

if (!$dniNode || !$codigoLibroNode) {
    responderFault("Faltan parámetros obligatorios: se requieren <dni> y <codigoLibro>.");
}

$dni = trim($dniNode->textContent);
$codigoLibro = trim($codigoLibroNode->textContent);

if ($dni === "") {
    responderFault("El DNI no puede estar vacío.");
}

if ($codigoLibro === "") {
    responderFault("El código de libro no puede estar vacío.");
}

// ─────────────────────────────────────────────────────────────────────────────
// PASO 4: Lógica de negocio — decidir si se puede prestar el libro
// ─────────────────────────────────────────────────────────────────────────────

// Si el libro está en la lista de no disponibles, no se puede prestar.
// OJO: esto devuelve una RESPUESTA NORMAL (puede_prestar=false), NO un Fault.
// Un Fault es un error de protocolo o validación; este es un resultado legítimo de negocio.
if (in_array($codigoLibro, $librosNoDisponibles, true)) {
    responderSOAPPrestamo(
        false,
        "El libro '$codigoLibro' no está disponible para préstamo (dado de baja o deteriorado).",
        0
    );
}

// En un sistema real comprobaríamos en BD cuántos préstamos activos tiene el usuario.
// Aquí simulamos: cualquier DNI válido con cualquier libro disponible puede pedir préstamo.
// El plazo estándar de la biblioteca es 14 días.
responderSOAPPrestamo(
    true,
    "Préstamo autorizado. El libro '$codigoLibro' puede ser prestado al usuario con DNI $dni.",
    14
);
?>
