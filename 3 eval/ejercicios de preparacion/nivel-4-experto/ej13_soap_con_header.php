<?php
/*
 * EJ13 — Servicio SOAP con Header de sesión
 *
 * SERVICIO: Consulta de notas de alumnos
 * OPERACIÓN: consultarNota(dni, asignatura) → nota (float), mensaje (string)
 *
 * El Header SOAP debe contener:
 *   <sesion><token>PROFESOR-2025</token></sesion>
 *
 * Tokens válidos: "PROFESOR-2025", "ADMIN-9999"
 * Notas simuladas (hardcoded):
 *   - DNI "11111111A", asignatura "Servidor" → 8.5
 *   - DNI "22222222B", asignatura "Cliente"  → 7.0
 *   - Cualquier otra combinación → nota 5.0 (aprobado por defecto)
 *
 * Si el token no es válido → SOAP Fault
 * Si faltan parámetros     → SOAP Fault
 */
header("Content-Type: text/xml; charset=utf-8");

// TODO 1: implementa responderSOAP($nota, $mensaje)
//         <consultarNotaResponse><nota>...</nota><mensaje>...</mensaje></consultarNotaResponse>
function responderSOAP($nota, $mensaje) {
    // TODO: echo Envelope XML con <consultarNotaResponse> + exit
    exit;
}

// TODO 2: implementa responderFault($mensaje) — estructura estándar SOAP Fault
function responderFault($mensaje) {
    // TODO: echo Envelope XML con <soap:Fault> + exit
    exit;
}

// TODO 3: valida que el método sea POST

// TODO 4: lee y parsea el XML (bloque estándar DOMDocument + DOMXPath)

// TODO 5: lee el Header y extrae el token
//         Si no hay Header → Fault
//         Si el token no está en los válidos → Fault "Token no válido"
$tokensValidos = ["PROFESOR-2025", "ADMIN-9999"];

// TODO 6: lee el Body, extrae la operación "consultarNota"
//         extrae los parámetros <dni> y <asignatura>
//         si faltan → Fault

// TODO 7: lógica de notas
//         Usa la tabla hardcoded de arriba
//         Llama a responderSOAP($nota, "Nota de $asignatura para $dni: $nota")
$notasSimuladas = [
    "11111111A" => ["Servidor" => 8.5, "Cliente" => 6.5],
    "22222222B" => ["Cliente"  => 7.0, "Servidor" => 9.0],
];
?>
