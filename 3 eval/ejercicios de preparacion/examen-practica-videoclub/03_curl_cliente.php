<?php
/*
 * PARTE 3 — Cliente cURL: llamadas HTTP desde PHP
 * Tiempo estimado: 15 minutos
 *
 * Ajusta estas URLs si trabajas en una ruta distinta localmente.
 */
$urlRest = "http://localhost/videoclub/02_rest_servidor.php";
$urlSoap = "http://localhost/videoclub/01_soap_servidor.php";

function ejecutarCurl($url, $opciones = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    foreach ($opciones as $clave => $valor) {
        curl_setopt($ch, $clave, $valor);
    }
    
    $respuesta  = curl_exec($ch);
    $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [$codigoHttp, $respuesta];
}

echo "=== PARTE 3: Cliente cURL ===\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// TODO 1: GET a la API REST → Lista todas las películas
// ─────────────────────────────────────────────────────────────────────────────
echo "--- 1. GET todas las películas ---\n";
[$codigoGet, $cuerpoGet] = ejecutarCurl("$urlRest/peliculas");
echo "GET /peliculas → HTTP $codigoGet\n";

$lista = json_decode($cuerpoGet, true) ?: [];
foreach ($lista as $p) {
    echo "  - ID {$p['id']}: {$p['titulo']} — Director: {$p['director']} ({$p['precio_alquiler']}€)\n";
}


// ─────────────────────────────────────────────────────────────────────────────
// TODO 2: POST a la API REST → Crea una película nueva
// ─────────────────────────────────────────────────────────────────────────────
echo "\n--- 2. POST crear película (Inception) ---\n";
$nuevaPeli = [
    "titulo"          => "Inception",
    "director"        => "Christopher Nolan",
    "genero"          => "Ciencia Ficción",
    "precio_alquiler" => 3.20,
    "copias"          => 4
];
$jsonBody = json_encode($nuevaPeli);

$opcionesPost = [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => $jsonBody,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Content-Length: " . strlen($jsonBody)
    ]
];

[$codigoPost, $cuerpoPost] = ejecutarCurl("$urlRest/peliculas", $opcionesPost);
echo "POST /peliculas → HTTP $codigoPost\n";

$creada = json_decode($cuerpoPost, true);
if (isset($creada["id"])) {
    echo "  Creada con éxito: '{$creada['titulo']}' con ID {$creada['id']}\n";
} else {
    echo "  Error devuelto por el servidor: $cuerpoPost\n";
}


// ─────────────────────────────────────────────────────────────────────────────
// TODO 3: Llamada SOAP via cURL → consultarDisponibilidad("Matrix")
// ─────────────────────────────────────────────────────────────────────────────
echo "\n--- 3. Llamada SOAP: consultarDisponibilidad('Matrix') ---\n";

$envelopeXml = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <consultarDisponibilidad>
      <titulo>Matrix</titulo>
    </consultarDisponibilidad>
  </soap:Body>
</soap:Envelope>';

$opcionesSoap = [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => $envelopeXml,
    CURLOPT_HTTPHEADER => [
        "Content-Type: text/xml; charset=utf-8",
        "SOAPAction: consultarDisponibilidad",
        "Content-Length: " . strlen($envelopeXml)
    ]
];

[$codigoSoap, $cuerpoSoap] = ejecutarCurl($urlSoap, $opcionesSoap);
echo "SOAP consultarDisponibilidad → HTTP $codigoSoap\n";

if ($codigoSoap === 200) {
    $dom = new DOMDocument();
    if ($dom->loadXML($cuerpoSoap)) {
        $copias = $dom->getElementsByTagName("copias")->item(0)->nodeValue ?? "0";
        $precio = $dom->getElementsByTagName("precio_alquiler")->item(0)->nodeValue ?? "0.00";
        $disp   = $dom->getElementsByTagName("disponible")->item(0)->nodeValue ?? "false";
        echo "  Resultados extraídos del XML:\n";
        echo "    - Copias en almacén: $copias\n";
        echo "    - Precio de alquiler: {$precio}€\n";
        echo "    - ¿Está disponible?: $disp\n";
    }
} else {
    echo "  Error de llamada o SOAP Fault detectado: $cuerpoSoap\n";
}
?>