<?php
/*
 * OBJETIVO DEL EJERCICIO: Implementar un servicio SOAP completo de validación
 *                         de acceso a examen, con validación robusta de datos.
 * CONCEPTO QUE ENTRENA: Distinción entre SOAP Fault (error de protocolo/validación)
 *                       y respuesta negativa de negocio (permitido=false).
 * PARA EL EXAMEN: Si los datos son inválidos (edad no es número, nombre vacío)
 *                 → usamos SOAP Fault. Si los datos son válidos pero el alumno
 *                 no cumple las condiciones → devolvemos permitido=false (respuesta normal).
 *                 Son dos situaciones completamente diferentes.
 */
header("Content-Type: text/xml; charset=utf-8");

// ─────────────────────────────────────────────────────────────────────────────
// Funciones de respuesta XML
// ─────────────────────────────────────────────────────────────────────────────

// Respuesta exitosa de la validación — siempre devuelve algo (true o false + mensaje).
// Esto es un RESULTADO, no un error. Aunque permitido sea false, no es un Fault.
function responderSOAPAcceso($permitido, $mensaje) {
    $booleanoXML = $permitido ? "true" : "false";

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <validarAccesoResponse>
            <permitido>' . $booleanoXML . '</permitido>
            <mensaje>' . htmlspecialchars($mensaje, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</mensaje>
            </validarAccesoResponse>
        </soap:Body>
        </soap:Envelope>';
    exit;
}

// Error de protocolo o validación de datos — el cliente envió algo incorrecto.
// Un Fault interrumpe el flujo: indica que la petición estaba mal formada o
// que se incumplió una regla de protocolo (como enviar un método diferente a POST).
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
// Validación del método HTTP
// ─────────────────────────────────────────────────────────────────────────────

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderFault("Este servicio SOAP solo acepta peticiones POST.");
}

// ─────────────────────────────────────────────────────────────────────────────
// Lectura y parseo del XML recibido
// ─────────────────────────────────────────────────────────────────────────────

$xmlRecibido = file_get_contents("php://input");

if (trim($xmlRecibido) === "") {
    responderFault("No se recibió ningún XML en el cuerpo de la petición.");
}

libxml_use_internal_errors(true);

$dom = new DOMDocument();
if (!$dom->loadXML($xmlRecibido)) {
    responderFault("El XML recibido no está bien formado.");
}

$xpath = new DOMXPath($dom);
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

// ─────────────────────────────────────────────────────────────────────────────
// Navegamos al Body para encontrar la operación
// ─────────────────────────────────────────────────────────────────────────────

$body = $xpath->query("//soap:Body")->item(0);

if (!$body) {
    responderFault("No se encontró el elemento Body en el Envelope SOAP.");
}

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

if ($operacion !== "validarAcceso") {
    responderFault("La operación '$operacion' no está definida en este servicio. Use 'validarAcceso'.");
}

// ─────────────────────────────────────────────────────────────────────────────
// Extraer los tres parámetros del cuerpo
// ─────────────────────────────────────────────────────────────────────────────

$nombreNode      = $operacionNode->getElementsByTagName("nombre")->item(0);
$edadNode        = $operacionNode->getElementsByTagName("edad")->item(0);
$matriculadoNode = $operacionNode->getElementsByTagName("matriculado")->item(0);

if (!$nombreNode || !$edadNode || !$matriculadoNode) {
    responderFault("Faltan parámetros obligatorios. Se requieren: nombre, edad y matriculado.");
}

$nombre      = trim($nombreNode->textContent);
$edadTexto   = trim($edadNode->textContent);
$matriculadoTexto = trim($matriculadoNode->textContent);

// ─────────────────────────────────────────────────────────────────────────────
// Validación de tipos y formatos — si algo no es válido, es un FAULT
// (el cliente envió datos incorrectos, no una situación de negocio normal)
// ─────────────────────────────────────────────────────────────────────────────

if ($nombre === "") {
    responderFault("El campo 'nombre' no puede estar vacío.");
}

// La edad debe ser un número entero positivo
if (!ctype_digit($edadTexto) || (int)$edadTexto <= 0) {
    responderFault("El campo 'edad' debe ser un número entero positivo. Valor recibido: '$edadTexto'.");
}

$edad = (int)$edadTexto;

// El campo matriculado debe ser exactamente "true" o "false" (booleano XML)
if ($matriculadoTexto !== "true" && $matriculadoTexto !== "false") {
    responderFault("El campo 'matriculado' debe ser 'true' o 'false'. Valor recibido: '$matriculadoTexto'.");
}

$matriculado = ($matriculadoTexto === "true");

// ─────────────────────────────────────────────────────────────────────────────
// Lógica de negocio — a partir de aquí los datos son válidos
// Las condiciones de acceso devuelven respuestas normales (no Faults)
// ─────────────────────────────────────────────────────────────────────────────

// Condición 1: debe estar matriculado
if (!$matriculado) {
    // No es un error del protocolo — es un resultado de negocio legítimo
    responderSOAPAcceso(
        false,
        "$nombre no está matriculado/a en el curso. Solo los alumnos matriculados pueden presentarse al examen."
    );
}

// Condición 2: debe tener más de 16 años
if ($edad <= 16) {
    responderSOAPAcceso(
        false,
        "$nombre tiene $edad años. Es necesario tener más de 16 años para presentarse al examen."
    );
}

// Si llega aquí, cumple las dos condiciones — acceso autorizado
responderSOAPAcceso(
    true,
    "$nombre está matriculado/a y tiene $edad años. Acceso al examen AUTORIZADO."
);
?>
