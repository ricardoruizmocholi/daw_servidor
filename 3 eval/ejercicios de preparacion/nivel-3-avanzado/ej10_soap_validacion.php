<?php
/*
 * OBJETIVO DEL EJERCICIO: SOAP con validación robusta — Fault vs respuesta negativa
 * CONCEPTO QUE ENTRENA: Cuándo usar SOAP Fault y cuándo una respuesta normal con resultado negativo
 * PARA EL EXAMEN: Esta distinción es la que más confunde en el examen:
 *   - Fault → datos INVÁLIDOS (formato incorrecto, campo que falta, método incorrecto)
 *   - Respuesta normal negativa → datos VÁLIDOS pero resultado NO favorable (temperatura fuera de rango razonable, etc.)
 *
 * SERVICIO: Conversor de temperaturas
 * Operación: convertirTemperatura(valor, de, a)
 *   - de/a: "celsius", "fahrenheit", "kelvin"
 *   - Devuelve: resultado (número) y unidad (string)
 *
 * FÓRMULAS:
 *   Celsius    → Fahrenheit: (C × 9/5) + 32
 *   Fahrenheit → Celsius:    (F − 32) × 5/9
 *   Celsius    → Kelvin:     C + 273.15
 *   Kelvin     → Celsius:    K − 273.15
 *   Fahrenheit → Kelvin:     ((F − 32) × 5/9) + 273.15
 *   Kelvin     → Fahrenheit: ((K − 273.15) × 9/5) + 32
 *
 * TIEMPO ESTIMADO: 20 minutos
 */

// TODO 1: header Content-Type XML

// ─────────────────────────────────────────────────────────────────────────────
// TODO 2: Implementa responderSOAP($resultado, $unidad)
//         Estructura de respuesta:
//         <convertirTemperaturaResponse>
//           <resultado>...</resultado>
//           <unidad>...</unidad>
//         </convertirTemperaturaResponse>
// ─────────────────────────────────────────────────────────────────────────────
function responderSOAP($resultado, $unidad) {
    // TODO 2
}

// TODO 3: Implementa responderFault($mensaje) — igual que en los otros ejercicios
function responderFault($mensaje) {
    // TODO 3
}

// ─────────────────────────────────────────────────────────────────────────────
// TODO 4: Valida método POST, lee XML, parsea con DOMDocument + DOMXPath
//         (igual que en ej06 — este bloque es siempre igual en todos los SOAP)
// ─────────────────────────────────────────────────────────────────────────────

// TODO 4: validar POST

// TODO 4: leer y parsear XML

// TODO 4: navegar al Body y obtener la operación

// ─────────────────────────────────────────────────────────────────────────────
// TODO 5: Extrae los tres parámetros: valor, de, a
//         Si alguno falta → Fault
// ─────────────────────────────────────────────────────────────────────────────


// ─────────────────────────────────────────────────────────────────────────────
// TODO 6: Valida los datos — si algo es inválido → Fault (no respuesta negativa)
//         a) "valor" debe ser numérico → is_numeric()
//         b) "de" debe ser uno de: "celsius", "fahrenheit", "kelvin"
//         c) "a" debe ser uno de: "celsius", "fahrenheit", "kelvin"
//         d) "de" y "a" no pueden ser iguales (convertir celsius a celsius no tiene sentido)
// ─────────────────────────────────────────────────────────────────────────────


// ─────────────────────────────────────────────────────────────────────────────
// TODO 7: Implementa la conversión con un switch anidado o una función
//         Convierte $valor de $de a $a usando las fórmulas del encabezado.
//         Redondea a 2 decimales: round($resultado, 2)
//         Llama a responderSOAP($resultado, $a)
// ─────────────────────────────────────────────────────────────────────────────


?>

<!--
    SOLUCIÓN:
    ─────────────────────────────────────────────────────────────────────────────

    TODO 1:  header("Content-Type: text/xml; charset=utf-8");

    TODO 2:
        function responderSOAP($resultado, $unidad) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <convertirTemperaturaResponse>
                <resultado>' . $resultado . '</resultado>
                <unidad>' . htmlspecialchars($unidad, ENT_XML1, 'UTF-8') . '</unidad>
                </convertirTemperaturaResponse>
            </soap:Body>
            </soap:Envelope>';
            exit;
        }

    TODO 3:
        function responderFault($mensaje) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <soap:Fault>
                <faultcode>SOAP-ENV:Client</faultcode>
                <faultstring>' . htmlspecialchars($mensaje, ENT_XML1, 'UTF-8') . '</faultstring>
                </soap:Fault>
            </soap:Body>
            </soap:Envelope>';
            exit;
        }

    TODO 4 (bloque estándar — memorízalo):
        if ($_SERVER["REQUEST_METHOD"] !== "POST") responderFault("Solo POST.");
        $xml = file_get_contents("php://input");
        if (trim($xml) === "") responderFault("XML vacío.");
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!$dom->loadXML($xml)) responderFault("XML inválido.");
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");
        $body = $xpath->query("//soap:Body")->item(0);
        if (!$body) responderFault("No hay Body.");
        $opNode = null;
        foreach ($body->childNodes as $n) { if ($n instanceof DOMElement) { $opNode = $n; break; } }
        if (!$opNode) responderFault("No hay operación.");

    TODO 5:
        $valorNode = $opNode->getElementsByTagName("valor")->item(0);
        $deNode    = $opNode->getElementsByTagName("de")->item(0);
        $aNode     = $opNode->getElementsByTagName("a")->item(0);
        if (!$valorNode || !$deNode || !$aNode) responderFault("Faltan parámetros: valor, de, a.");
        $valor = trim($valorNode->textContent);
        $de    = trim($deNode->textContent);
        $a     = trim($aNode->textContent);

    TODO 6:
        $unidades = ["celsius", "fahrenheit", "kelvin"];
        if (!is_numeric($valor)) responderFault("'valor' debe ser numérico.");
        if (!in_array($de, $unidades)) responderFault("'de' debe ser celsius, fahrenheit o kelvin.");
        if (!in_array($a, $unidades))  responderFault("'a' debe ser celsius, fahrenheit o kelvin.");
        if ($de === $a) responderFault("'de' y 'a' no pueden ser iguales.");
        $valor = (float)$valor;

    TODO 7:
        // Convertir todo a Celsius primero, luego al destino
        $celsius = match($de) {
            "celsius"    => $valor,
            "fahrenheit" => ($valor - 32) * 5/9,
            "kelvin"     => $valor - 273.15,
        };
        $resultado = match($a) {
            "celsius"    => $celsius,
            "fahrenheit" => ($celsius * 9/5) + 32,
            "kelvin"     => $celsius + 273.15,
        };
        responderSOAP(round($resultado, 2), $a);

    ─────────────────────────────────────────────────────────────────────────────
-->
