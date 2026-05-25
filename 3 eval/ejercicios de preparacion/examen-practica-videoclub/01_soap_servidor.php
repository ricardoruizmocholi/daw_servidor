<?php
/*
 * PARTE 1 — Servidor SOAP: Videoclub
 * Tiempo estimado: 25 minutos
 *
 * OPERACIÓN 1: consultarDisponibilidad
 *   Entrada:  <titulo>Interstellar</titulo>
 *   Salida:   <copias>5</copias><precio_alquiler>3.50</precio_alquiler><disponible>true</disponible>
 *   Error:    Fault si la película no existe
 *
 * OPERACIÓN 2: alquilarPelicula
 *   Entrada:  <titulo>Matrix</titulo>
 *   Salida:   <confirmacion>Alquiler registrado</confirmacion><copias_restantes>6</copias_restantes>
 *   Error:    Fault si no existe o si copias === 0
 *
 * DATOS: peliculas.json (en este mismo directorio)
 */

// ─────────────────────────────────────────────────────────────────────────────
// TODO 1: Pon el header correcto para SOAP
// Pista: header("Content-Type: text/xml; charset=utf-8");
// ─────────────────────────────────────────────────────────────────────────────
// TODO 1
 header("Content-Type: text/xml; charset=utf-8");
 // ─────────────────────────────────────────────────────────────────────────────
 // TODO 2: Implementa responderFault($mensaje)
 //
 // Debe devolver un XML con esta estructura:
 //
 

// Pasos:
//   a) http_response_code(500);
//   b) echo el XML con el $mensaje dentro de <faultstring>
//      usa htmlspecialchars($mensaje, ENT_XML1, 'UTF-8') para escapar caracteres especiales
//   c) exit;
// ─────────────────────────────────────────────────────────────────────────────


// ─────────────────────────────────────────────────────────────────────────────
function responderFault($mensaje) {
    // TODO 2
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
// TODO 3: Implementa responderSOAP($etiquetaRespuesta, array $campos)
//
// $etiquetaRespuesta: nombre del tag de respuesta, ej: "consultarDisponibilidadResponse"
// $campos: array asociativo con los datos, ej: ["copias" => 5, "disponible" => "true"]
//
// Debe generar este XML (ejemplo):
// Pasos:
//   a) Recorre $campos con foreach y construye una cadena $contenido con los tags
//      Por cada par clave → valor: $contenido .= "<$clave>" . htmlspecialchars($valor) . "</$clave>";
//   b) echo el Envelope SOAP con $etiquetaRespuesta y $contenido interpolados
//      (usa variables PHP dentro del string, NO comillas simples)
//   c) exit;
// ─────────────────────────────────────────────────────────────────────────────
function responderSOAP($etiquetaRespuesta, array $campos) {
    // TODO 3
    http_response_code(200);
    
    $innerXml = "";
    foreach ($campos as $clave => $valor) {
        $innerXml .= "<$clave>" . htmlspecialchars((string)$valor, ENT_XML1, 'UTF-8') . "</$clave>";
    }
    
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
    echo '  <soap:Body>';
    echo "    <$etiquetaRespuesta>$innerXml</$etiquetaRespuesta>";
    echo '  </soap:Body>';
    echo '</soap:Envelope>';
    exit;
}


// ─────────────────────────────────────────────────────────────────────────────
// BLOQUE ESTÁNDAR — Leer y parsear la petición SOAP entrante
// (Este bloque es igual en todos los ejercicios SOAP — memorízalo)
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

$opNodo = null;
$bodyNodo = $xpath->query("//soap:Body")->item(0);
if ($bodyNodo) {
    foreach ($bodyNodo->childNodes as $nodo) {
        if ($nodo instanceof DOMElement) { $opNodo = $nodo; break; }
    }
}
if (!$opNodo) {
    responderFault("No se encontró ninguna operación en el Body SOAP.");
}

$operacion = $opNodo->localName;


// ─────────────────────────────────────────────────────────────────────────────
// TODO 4: Lee peliculas.json y decodifícalo en un array PHP
// Pista: $peliculas = json_decode(file_get_contents(__DIR__ . "/peliculas.json"), true) ?: [];
// ─────────────────────────────────────────────────────────────────────────────
// TODO 4
$archivoJson = __DIR__ . "/peliculas.json";
$peliculas = json_decode(file_get_contents($archivoJson), true) ?: [];

// ─────────────────────────────────────────────────────────────────────────────
// ENRUTADOR DE OPERACIONES
// ─────────────────────────────────────────────────────────────────────────────
switch ($operacion) {

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 5: Implementa "consultarDisponibilidad"
    //
    // Pasos:
    //   a) Extrae el título del $opNodo:
    //      $titulo = trim($opNodo->getElementsByTagName("titulo")->item(0)->nodeValue);
    //   b) Si $titulo está vacío → responderFault("Falta el parámetro <titulo>")
    //   c) Busca la película comparando en minúsculas:
    //      strtolower($p["titulo"]) === strtolower($titulo)
    //   d) Si no existe → responderFault("Película '$titulo' no encontrada")
    //   e) Si existe → responderSOAP("consultarDisponibilidadResponse", [
    //          "copias"          => $encontrada["copias"],
    //          "precio_alquiler" => $encontrada["precio_alquiler"],
    //          "disponible"      => ($encontrada["copias"] > 0) ? "true" : "false"
    //      ])
    // ─────────────────────────────────────────────────────────────────────────
    case "consultarDisponibilidad":
        // TODO 5
        $tituloNodo = $opNodo->getElementsByTagName("titulo")->item(0);
        if (!$tituloNodo) {
            responderFault("Falta el parámetro requerido <titulo>.");
        }
        
        $titulo = trim($tituloNodo->nodeValue);
        if ($titulo === "") {
            responderFault("El parámetro <titulo> no puede estar vacío.");
        }
        
        $encontrada = null;
        foreach ($peliculas as $p) {
            if (strtolower($p["titulo"]) === strtolower($titulo)) {
                $encontrada = $p;
                break;
            }
        }
        
        if (!$encontrada) {
            responderFault("Película '$titulo' no encontrada.");
        }
        
        responderSOAP("consultarDisponibilidadResponse", [
            "copias"          => $encontrada["copias"],
            "precio_alquiler" => number_format($encontrada["precio_alquiler"], 2, '.', ''),
            "disponible"      => ($encontrada["copias"] > 0) ? "true" : "false"
        ]);
        break;

    // ─────────────────────────────────────────────────────────────────────────
    // TODO 6: Implementa "alquilarPelicula"
    //
    // Pasos (similares a consultarDisponibilidad, pero además modifica el JSON):
    //   a) Extrae $titulo del $opNodo (igual que TODO 5a)
    //   b) Valida que no esté vacío
    //   c) Busca la película (guarda también el índice $indice con foreach ($peliculas as $i => $p))
    //   d) Si no existe → responderFault(...)
    //   e) Si copias === 0 → responderFault("No hay copias disponibles de '$titulo'")
    //   f) Reduce una copia: $peliculas[$indice]["copias"]--;
    //   g) Guarda el archivo:
    //      file_put_contents(__DIR__ . "/peliculas.json",
    //          json_encode($peliculas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    //   h) responderSOAP("alquilarPeliculaResponse", [
    //          "confirmacion"     => "Alquiler registrado",
    //          "copias_restantes" => $peliculas[$indice]["copias"]
    //      ])
    // ─────────────────────────────────────────────────────────────────────────
    case "alquilarPelicula":
        // TODO 6
        $tituloNodo = $opNodo->getElementsByTagName("titulo")->item(0);
        if (!$tituloNodo) {
            responderFault("Falta el parámetro requerido <titulo>.");
        }
        
        $titulo = trim($tituloNodo->nodeValue);
        if ($titulo === "") {
            responderFault("El parámetro <titulo> no puede estar vacío.");
        }
        
        $indice = null;
        foreach ($peliculas as $i => $p) {
            if (strtolower($p["titulo"]) === strtolower($titulo)) {
                $indice = $i;
                break;
            }
        }
        
        if ($indice === null) {
            responderFault("Película '$titulo' no encontrada.");
        }
        
        if ($peliculas[$indice]["copias"] <= 0) {
            responderFault("No hay copias disponibles de '$titulo'.");
        }
        
        // Decrementamos el número de copias por el alquiler
        $peliculas[$indice]["copias"]--;
        
        // Guardamos los cambios de vuelta al fichero JSON
        file_put_contents($archivoJson, json_encode($peliculas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        responderSOAP("alquilarPeliculaResponse", [
            "confirmacion"     => "Alquiler registrado",
            "copias_restantes" => $peliculas[$indice]["copias"]
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

TODO 2:
   

TODO 4:
    $peliculas = json_decode(file_get_contents(__DIR__ . "/peliculas.json"), true) ?: [];

TODO 5:
    case "consultarDisponibilidad":
        $tituloNodo = $opNodo->getElementsByTagName("titulo")->item(0);
        if (!$tituloNodo) responderFault("Falta el parámetro <titulo>.");
        $titulo = trim($tituloNodo->nodeValue);
        if ($titulo === "") responderFault("El parámetro <titulo> no puede estar vacío.");
        $encontrada = null;
        foreach ($peliculas as $p) {
            if (strtolower($p["titulo"]) === strtolower($titulo)) { $encontrada = $p; break; }
        }
        if (!$encontrada) responderFault("Película '$titulo' no encontrada.");
        responderSOAP("consultarDisponibilidadResponse", [
            "copias"          => $encontrada["copias"],
            "precio_alquiler" => $encontrada["precio_alquiler"],
            "disponible"      => ($encontrada["copias"] > 0) ? "true" : "false"
        ]);
        break;

TODO 6:
    case "alquilarPelicula":
        $tituloNodo = $opNodo->getElementsByTagName("titulo")->item(0);
        if (!$tituloNodo) responderFault("Falta el parámetro <titulo>.");
        $titulo = trim($tituloNodo->nodeValue);
        if ($titulo === "") responderFault("El parámetro <titulo> no puede estar vacío.");
        $indice = null;
        foreach ($peliculas as $i => $p) {
            if (strtolower($p["titulo"]) === strtolower($titulo)) { $indice = $i; break; }
        }
        if ($indice === null) responderFault("Película '$titulo' no encontrada.");
        if ($peliculas[$indice]["copias"] === 0) responderFault("No hay copias disponibles de '$titulo'.");
        $peliculas[$indice]["copias"]--;
        file_put_contents(__DIR__ . "/peliculas.json",
            json_encode($peliculas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        responderSOAP("alquilarPeliculaResponse", [
            "confirmacion"     => "Alquiler registrado",
            "copias_restantes" => $peliculas[$indice]["copias"]
        ]);
        break;

═══════════════════════════════════════════════════════════════════════════════
-->
