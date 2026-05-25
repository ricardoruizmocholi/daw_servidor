<?php
/*
 * PARTE 3 — Cliente cURL: llamadas HTTP desde PHP
 * Tiempo estimado: 20 minutos
 *
 * OBJETIVO: Hacer las tres llamadas siguientes con cURL:
 *   1. GET a la API REST → lista de productos
 *   2. POST a la API REST → crea un producto nuevo
 *   3. Llamada SOAP via cURL → llama a obtenerPrecio("Camiseta")
 *
 * CONCEPTOS CLAVE DE cURL:
 *   curl_init($url)                        → inicializa la conexión
 *   curl_setopt($ch, OPCION, VALOR)        → configura la petición
 *   curl_exec($ch)                         → ejecuta y devuelve la respuesta
 *   curl_getinfo($ch, CURLINFO_HTTP_CODE)  → lee el código HTTP de la respuesta
 *   curl_close($ch)                        → libera la conexión
 *
 * OPCIONES ÚTILES:
 *   CURLOPT_RETURNTRANSFER => true         → devuelve la respuesta como string (no la imprime)
 *   CURLOPT_POST           => true         → usa método POST
 *   CURLOPT_POSTFIELDS     => $datos       → cuerpo del POST
 *   CURLOPT_HTTPHEADER     => [...]        → cabeceras HTTP
 *   CURLOPT_TIMEOUT        => 10           → timeout en segundos
 *
 * AJUSTA ESTAS URLs A TU ENTORNO LOCAL:
 */
$urlBase = "http://localhost/examen/04_rest_servidor.php";
$urlSoap = "http://localhost/examen/02_soap_servidor.php";

// ─────────────────────────────────────────────────────────────────────────────
// FUNCIÓN AUXILIAR (ya implementada — no modificar)
// Lanza una petición cURL y devuelve [$codigoHttp, $cuerpRespuesta]
// ─────────────────────────────────────────────────────────────────────────────
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
// TODO 1: GET — Listar todos los productos
//
// GET es el método por defecto de cURL, así que NO necesitas opciones extra.
//
// Pasos:
//   a) Llama a ejecutarCurl("$urlBase/productos")
//      [$codigo, $cuerpo] = ejecutarCurl("$urlBase/productos");
//   b) Muestra el código HTTP:
//      echo "GET /productos → HTTP $codigo\n";
//   c) Decodifica el JSON y muestra cada producto:
//      $lista = json_decode($cuerpo, true);
//      foreach ($lista as $p) {
//          echo "  - {$p['nombre']} ({$p['categoria']}) → {$p['precio']}€\n";
//      }
// ─────────────────────────────────────────────────────────────────────────────
echo "--- 1. GET todos los productos ---\n";

// TODO 1
[$codigo, $cuerpo] = ejecutarCurl("$urlBase/productos");

echo "GET /productos → HTTP $codigo\n";

$lista = json_decode($cuerpo, true) ? : [];

foreach($lista as $p){
    echo "  - {$p['nombre']} ({$p['categoria']}) → {$p['precio']}€\n";
}


// ─────────────────────────────────────────────────────────────────────────────
// TODO 2: POST — Crear un producto nuevo
//
// Pasos:
//   a) Define el nuevo producto como array:
//      $nuevo = ["nombre"=>"Auriculares","categoria"=>"Electrónica","precio"=>29.99,"stock"=>30];
//   b) Conviértelo a JSON:
//      $json = json_encode($nuevo);
//   c) Define las opciones cURL para POST con JSON:
//      $opciones = [
//          CURLOPT_POST       => true,
//          CURLOPT_POSTFIELDS => $json,
//          CURLOPT_HTTPHEADER => [
//              "Content-Type: application/json",
//              "Content-Length: " . strlen($json)
//          ]
//      ];
//   d) Llama a ejecutarCurl("$urlBase/productos", $opciones)
//   e) Muestra el código HTTP y el producto creado:
//      echo "POST /productos → HTTP $codigo\n";
//      $creado = json_decode($cuerpo, true);
//      echo "  Creado: {$creado['nombre']} con ID {$creado['id']}\n";
// ─────────────────────────────────────────────────────────────────────────────
echo "\n--- 2. POST crear producto ---\n";
// TODO 2
$nuevo = ["nombre" => "Auriculares", "categoria" => "Electrónica", "precio" => 29.99, "stock" => 30];
$json = json_encode($nuevo);

$opcionesPost = [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Content-Length: " . strlen($json)
    ]
];

[$codigoPost, $cuerpoPost] = ejecutarCurl("$urlBase/productos", $opcionesPost);
echo "POST /productos → HTTP $codigoPost\n";

$creado = json_decode($cuerpoPost, true);
if (isset($creado['id'])) {
    echo "  Creado: {$creado['nombre']} con ID {$creado['id']}\n";
} else {
    echo "  Error al crear: $cuerpoPost\n";
}


// ─────────────────────────────────────────────────────────────────────────────
// TODO 3: Llamada SOAP via cURL — obtenerPrecio("Camiseta")
//
// SOAP usa siempre POST con Content-Type: text/xml y una cabecera SOAPAction.
// El cuerpo es un envelope XML con la operación y los parámetros.
//
// Pasos:
//   a) Define las opciones cURL:
//      $opciones = [
//          CURLOPT_POST       => true,
//          CURLOPT_POSTFIELDS => $envelopeXml,
//          CURLOPT_HTTPHEADER => [
//              "Content-Type: text/xml; charset=utf-8",
//              "SOAPAction: obtenerPrecio",
//              "Content-Length: " . strlen($envelopeXml)
//          ]
//      ];
//   b) Llama a ejecutarCurl($urlSoap, $opciones)
//   c) Muestra el código HTTP:
//      echo "SOAP obtenerPrecio → HTTP $codigo\n";
//   d) Parsea el XML de respuesta con DOMDocument:
//      $dom = new DOMDocument();
//      $dom->loadXML($cuerpo);
//      $precio = $dom->getElementsByTagName("precio")->item(0)->nodeValue;
//      $nombre = $dom->getElementsByTagName("nombre")->item(0)->nodeValue;
//      echo "  Precio de '$nombre': {$precio}€\n";
// ─────────────────────────────────────────────────────────────────────────────
echo "\n--- 3. Llamada SOAP: obtenerPrecio('Camiseta') ---\n";

// El envelope SOAP (ya construido — solo tienes que enviarlo)
$envelopeXml = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <obtenerPrecio>
      <nombre>Camiseta</nombre>
    </obtenerPrecio>
  </soap:Body>
</soap:Envelope>';

// TODO 3: envía $envelopeXml al $urlSoap con cURL y muestra el resultado

$envelopeXml = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <obtenerPrecio>
      <nombre>Camiseta</nombre>
    </obtenerPrecio>
  </soap:Body>
</soap:Envelope>';

$opcionesSoap = [
    CURLOPT_POST       => true,
    CURLOPT_POSTFIELDS => $envelopeXml,
    CURLOPT_HTTPHEADER => [
        "Content-Type: text/xml; charset=utf-8",
        "SOAPAction: obtenerPrecio",
        "Content-Length: " . strlen($envelopeXml)
    ]
];

[$codigoSoap, $cuerpoSoap] = ejecutarCurl($urlSoap, $opcionesSoap);
echo "SOAP obtenerPrecio → HTTP $codigoSoap\n";

if ($codigoSoap === 200) {
    $dom = new DOMDocument();
    $dom->loadXML($cuerpoSoap);
    $precio = $dom->getElementsByTagName("precio")->item(0)->nodeValue;
    $nombre = $dom->getElementsByTagName("nombre")->item(0)->nodeValue;
    echo "  Precio de '$nombre': {$precio}€\n";
} else {
    echo "  SOAP Fault o error detectado. Respuesta: $cuerpoSoap\n";
}
?>

<!--
═══════════════════════════════════════════════════════════════════════════════
  SOLUCIÓN COMPLETA — Mírala solo si ya lo has intentado
═══════════════════════════════════════════════════════════════════════════════

TODO 1:
    [$codigo, $cuerpo] = ejecutarCurl("$urlBase/productos");
    echo "GET /productos → HTTP $codigo\n";
    $lista = json_decode($cuerpo, true);
    foreach ($lista as $p) {
        echo "  - {$p['nombre']} ({$p['categoria']}) → {$p['precio']}€\n";
    }

TODO 2:
    $nuevo  = ["nombre"=>"Auriculares","categoria"=>"Electrónica","precio"=>29.99,"stock"=>30];
    $json   = json_encode($nuevo);
    $opciones = [
        CURLOPT_POST       => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Content-Length: " . strlen($json)
        ]
    ];
    [$codigo, $cuerpo] = ejecutarCurl("$urlBase/productos", $opciones);
    echo "POST /productos → HTTP $codigo\n";
    $creado = json_decode($cuerpo, true);
    echo "  Creado: {$creado['nombre']} con ID {$creado['id']}\n";

TODO 3:
    $opciones = [
        CURLOPT_POST       => true,
        CURLOPT_POSTFIELDS => $envelopeXml,
        CURLOPT_HTTPHEADER => [
            "Content-Type: text/xml; charset=utf-8",
            "SOAPAction: obtenerPrecio",
            "Content-Length: " . strlen($envelopeXml)
        ]
    ];
    [$codigo, $cuerpo] = ejecutarCurl($urlSoap, $opciones);
    echo "SOAP obtenerPrecio → HTTP $codigo\n";
    $dom = new DOMDocument();
    $dom->loadXML($cuerpo);
    $precio = $dom->getElementsByTagName("precio")->item(0)->nodeValue;
    $nombre = $dom->getElementsByTagName("nombre")->item(0)->nodeValue;
    echo "  Precio de '$nombre': {$precio}€\n";

═══════════════════════════════════════════════════════════════════════════════
-->
