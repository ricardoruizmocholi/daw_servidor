<?php
/*
 * EJ06 — Servicio SOAP mínimo
 * Operación: saludar(nombre) → "<saludarResponse><saludo>¡Hola, X!</saludo></saludarResponse>"
 * Concepto: DOMDocument, DOMXPath, construcción de XML SOAP
 */

// TODO 1: header Content-Type XML ("text/xml; charset=utf-8")

// TODO 2: implementa responderSOAP($saludo)
//         Devuelve Envelope > Body > <saludarResponse><saludo>...</saludo></saludarResponse>
function responderSOAP($saludo) {
    // TODO: echo '<?xml version...' + Envelope + Body + <saludarResponse> + exit
    exit;
}

// TODO 3: implementa responderFault($mensaje)
//         Devuelve Envelope > Body > <soap:Fault><faultcode>...<faultstring>...
function responderFault($mensaje) {
    // TODO: echo '<?xml version...' + Envelope + Body + <soap:Fault> + exit
    exit;
}

// TODO 4: valida que el método sea POST (si no → Fault)

// TODO 5: lee file_get_contents("php://input"), valida que no esté vacío
//         crea DOMDocument, llama a loadXML(), si falla → Fault

// TODO 6: crea DOMXPath, registra namespace "soap",
//         navega al Body, obtén el primer hijo (la operación),
//         extrae el elemento <nombre>

// TODO 7: valida que $nombre no esté vacío → Fault si lo está
//         llama a responderSOAP("¡Hola, $nombre!")
?>
