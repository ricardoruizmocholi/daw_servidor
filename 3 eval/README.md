# 3ª Evaluación — Desarrollo en Entorno Servidor

## ¿Qué se practica aquí?
Dos máquinas hablan a través de la red usando protocolos estándar. Hay un **cliente** (el que pide) y un **servidor** (el que responde). Aprenderás a construir y consumir ambos lados usando los dos grandes protocolos: **REST** (moderno, JSON) y **SOAP** (formal, XML).

## Carpetas del tema

| Carpeta | Contenido |
|---|---|
| `001-tecnologias y protocolos implicados` | API REST con PHP y MySQL — tienda online |
| `002-Estandares y arquitectura actuales` | Protocolo SOAP y WSDL — calculadora |
| `003-Generacion de un servicio` | Design contract-first: WSDL → implementación |
| `004-interfaces de un servicio` | SOAP con Headers para metadatos de sesión |
| `005-Consumo de un servicio web` | Consumo de APIs REST + especificación OpenAPI |
| `007-Generacion dinamica de paginas interactivas` | HTML dinámico con Fetch y PHP |
| `008-Obtecion remota de informacion` | cURL: el servidor llama a APIs externas |
| `009-Modificacion de la estuctura y contenido` | Fragmentos HTML generados en servidor |
| `ejercicios de preparacion/` | Ejercicios ordenados por nivel + simulacros de examen |

---

## Diagrama general: cómo fluye la información

```
CLIENTE (Navegador / HTML + JS)          SERVIDOR (PHP)
         │                                     │
         │  fetch(url, { method, headers,  ──► │  $_SERVER["REQUEST_METHOD"]
         │               body })               │  $_SERVER["PATH_INFO"]
         │                                     │  file_get_contents("php://input")
         │                                     │  lógica + archivo JSON o BD
         │  ◄──── HTTP Response ───────────── │  http_response_code(201)
         │  status + body (JSON o XML)         │  echo json_encode($datos)
```

```
CLIENTE PHP (cURL)                       SERVIDOR PHP
         │                                     │
         │  curl_init($url)              ────► │  (cualquier servidor web)
         │  curl_setopt(...)                   │
         │  curl_exec($ch)               ◄──── │  respuesta como string
```

---

# SINTAXIS DE REFERENCIA COMPLETA

---

## 1. PHP como servidor REST

### 1.1 Cabecera obligatoria

```php
header("Content-Type: application/json; charset=utf-8");
```
Siempre es **la primera línea** del archivo. Sin esta cabecera el cliente no sabe interpretar la respuesta como JSON.

---

### 1.2 Leer el método HTTP y la URL

```php
$metodo = $_SERVER["REQUEST_METHOD"];   // "GET", "POST", "PUT", "DELETE", "PATCH"

$ruta = $_SERVER["PATH_INFO"] ?? "";    // "/peliculas/3" → lo que va después del .php
$partes = explode("/", trim($ruta, "/")); // ["peliculas", "3"]

$recurso = $partes[0] ?? "";            // "peliculas"
$id      = $partes[1] ?? null;          // "3" o null si no hay
$id      = ($id !== null) ? (int)$id : null;  // convertir a entero
```

> La URL `http://localhost/api.php/peliculas/3` da `PATH_INFO = /peliculas/3`

---

### 1.3 Leer el body JSON (POST / PUT)

```php
$raw  = file_get_contents("php://input");  // lee el cuerpo de la petición como string
$body = json_decode($raw, true);           // convierte JSON → array PHP asociativo
                                           // el segundo parámetro true = array, false/omitido = objeto
```

---

### 1.4 Enviar respuesta JSON

```php
function responder($codigo, $datos = null) {
    http_response_code($codigo);                          // fija el status HTTP
    if ($datos !== null) {
        echo json_encode($datos, JSON_UNESCAPED_UNICODE); // array PHP → JSON string
    }
    exit;                                                 // cierra la respuesta
}

// Ejemplos de uso:
responder(200, $productos);                              // OK con datos
responder(201, $nuevoRecurso);                           // Created con el recurso nuevo
responder(204);                                          // No Content — sin body (DELETE)
responder(400, ["error" => "Faltan campos"]);            // Bad Request
responder(404, ["error" => "No encontrado"]);            // Not Found
responder(405, ["error" => "Método no permitido"]);      // Method Not Allowed
```

**Flags útiles de `json_encode`:**
```php
json_encode($datos, JSON_UNESCAPED_UNICODE)              // no escapa tildes/ñ
json_encode($datos, JSON_PRETTY_PRINT)                   // formatea con indentación
json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)  // ambos combinados
```

---

### 1.5 Leer y guardar datos en un archivo JSON

```php
// Leer
$datos = json_decode(file_get_contents($archivo), true) ?: [];

// Guardar
file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
```

---

### 1.6 Patrón CRUD completo — switch por método

```php
switch ($metodo) {

    case "GET":
        if ($id === null) {
            responder(200, $items);               // GET /recursos → todos
        } else {
            foreach ($items as $item) {
                if ($item["id"] === $id) responder(200, $item);  // GET /recursos/{id}
            }
            responder(404, ["error" => "No encontrado"]);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        if (empty($body["campo"])) {
            responder(400, ["error" => "Faltan campos obligatorios"]);
        }
        $ids    = array_column($items, "id");         // extrae todos los ids como array
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;  // autoincremento manual
        $nuevo  = ["id" => $nuevoId, "campo" => $body["campo"]];
        $items[] = $nuevo;
        guardar($archivo, $items);
        responder(201, $nuevo);
        break;

    case "PUT":
        if ($id === null) responder(400, ["error" => "Se requiere ID en la URL"]);
        $body = json_decode(file_get_contents("php://input"), true);
        $indice = null;
        foreach ($items as $i => $item) {
            if ($item["id"] === $id) { $indice = $i; break; }
        }
        if ($indice === null) responder(404, ["error" => "No encontrado"]);
        $items[$indice] = ["id" => $id, "campo" => $body["campo"]]; // reemplaza todo
        guardar($archivo, $items);
        responder(200, $items[$indice]);
        break;

    case "DELETE":
        if ($id === null) responder(400, ["error" => "Se requiere ID en la URL"]);
        $indice = null;
        foreach ($items as $i => $item) {
            if ($item["id"] === $id) { $indice = $i; break; }
        }
        if ($indice === null) responder(404, ["error" => "No encontrado"]);
        array_splice($items, $indice, 1);  // elimina 1 elemento en la posición $indice
        guardar($archivo, $items);
        responder(204);                    // 204 = sin body, obligatorio en DELETE
        break;

    default:
        header("Allow: GET, POST, PUT, DELETE");
        responder(405, ["error" => "Método no permitido"]);
}
```

---

### 1.7 Tabla de status codes HTTP

| Código | Nombre | Cuándo usarlo |
|--------|--------|---------------|
| `200` | OK | GET o PUT que devuelve datos |
| `201` | Created | POST que crea un recurso nuevo |
| `204` | No Content | DELETE exitoso (sin body) |
| `400` | Bad Request | Faltan campos, JSON malformado |
| `404` | Not Found | Recurso no existe |
| `405` | Method Not Allowed | Método HTTP no soportado |
| `500` | Internal Server Error | Error en el servidor (BD, etc.) |

---

## 2. PHP como servidor SOAP

### 2.1 Cabecera obligatoria

```php
header("Content-Type: text/xml; charset=utf-8");
// SOAP habla XML, no JSON. El cliente espera XML de vuelta.
```

---

### 2.2 Funciones de respuesta XML

```php
// Respuesta OK — construye el envelope con los campos dados
function responderSOAP($etiquetaRespuesta, array $campos) {
    http_response_code(200);
    $contenido = "";
    foreach ($campos as $clave => $valor) {
        $contenido .= "<$clave>" . htmlspecialchars((string)$valor, ENT_XML1, 'UTF-8') . "</$clave>";
        //             ↑ tag XML     ↑ escapa < > & ' " para que el XML sea válido
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
    echo '  <soap:Body>';
    echo "    <$etiquetaRespuesta>$contenido</$etiquetaRespuesta>";  // usar "" para interpolar vars
    echo '  </soap:Body>';
    echo '</soap:Envelope>';
    exit;
}

// Respuesta de error — SOAP Fault
function responderFault($mensaje) {
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
```

> **Fault vs respuesta negativa:** Un Fault es cuando el cliente envió algo incorrecto (parámetro faltante, XML mal formado). Una respuesta negativa (`permitido=false`) es un resultado de negocio válido — no es un Fault.

---

### 2.3 Bloque estándar para leer la petición SOAP entrante

Este bloque es **idéntico** en todos los ejercicios SOAP. Memorízalo:

```php
// 1. Solo aceptar POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responderFault("SOAP solo acepta peticiones POST.");
}

// 2. Leer el XML del cuerpo
$rawXml = file_get_contents("php://input");
if (trim($rawXml) === "") {
    responderFault("El cuerpo de la petición está vacío.");
}

// 3. Parsear el XML con DOMDocument
libxml_use_internal_errors(true);   // suprime errores PHP de XML
$dom = new DOMDocument();
if (!$dom->loadXML($rawXml)) {      // carga el XML. Devuelve false si está mal formado
    responderFault("El XML recibido está mal formado.");
}

// 4. Navegar hasta el Body con XPath
$xpath = new DOMXPath($dom);
$xpath->registerNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");
//     ↑ necesario para que XPath entienda el prefijo "soap:"

$bodyNodo = $xpath->query("//soap:Body")->item(0);  // localiza el <soap:Body>

// 5. Encontrar la operación (primer hijo del Body)
$opNodo = null;
foreach ($bodyNodo->childNodes as $nodo) {
    if ($nodo instanceof DOMElement) { $opNodo = $nodo; break; }
}
if (!$opNodo) responderFault("No se encontró ninguna operación.");

$operacion = $opNodo->localName;  // "sumar", "consultarStock", etc.
```

---

### 2.4 Leer parámetros del Body

```php
// Leer un parámetro del nodo de la operación
$nodoParam = $opNodo->getElementsByTagName("nombre")->item(0);
if (!$nodoParam) responderFault("Falta el parámetro <nombre>.");

$valor = trim($nodoParam->nodeValue);   // o ->textContent (equivalentes)
if ($valor === "") responderFault("El parámetro <nombre> no puede estar vacío.");
```

---

### 2.5 Estructura del envelope SOAP — referencia visual

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>              <!-- OPCIONAL — metadatos: sesión, autenticación -->
    <token>abc123</token>
  </soap:Header>
  <soap:Body>                <!-- OBLIGATORIO — la operación y sus parámetros -->
    <consultarStock>
      <nombre>Camiseta</nombre>
    </consultarStock>
  </soap:Body>
</soap:Envelope>
```

---

### 2.6 Estructura de la respuesta SOAP

```xml
<!-- Respuesta OK -->
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <consultarStockResponse>
      <stock>50</stock>
      <disponible>true</disponible>
    </consultarStockResponse>
  </soap:Body>
</soap:Envelope>

<!-- Respuesta de error: SOAP Fault -->
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>SOAP-ENV:Client</faultcode>
      <faultstring>Producto 'X' no encontrado</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
```

---

## 3. cURL — PHP como cliente HTTP

cURL sirve para que **PHP llame a otro servidor** (REST o SOAP) desde el lado servidor.

### 3.1 Secuencia obligatoria

```php
$ch = curl_init($url);                               // 1. Inicializa la conexión
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // 2. SIEMPRE — devuelve la respuesta como string
curl_setopt($ch, CURLOPT_TIMEOUT, 10);               //    timeout total en segundos
// ... más opciones según el tipo de petición
$respuesta  = curl_exec($ch);                        // 3. Ejecuta y obtiene la respuesta
$codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE); //    lee el status HTTP de la respuesta
$error      = curl_error($ch);                       //    lee el error si hubo fallo de red
curl_close($ch);                                     // 4. Libera la conexión
```

---

### 3.2 GET (método por defecto — sin opciones extra)

```php
$ch = curl_init("http://localhost/api.php/productos");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$respuesta  = curl_exec($ch);
$codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$datos = json_decode($respuesta, true);
```

---

### 3.3 POST con JSON (para REST)

```php
$datos = ["nombre" => "Auriculares", "precio" => 29.99];
$json  = json_encode($datos);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,           true);         // activa POST
curl_setopt($ch, CURLOPT_POSTFIELDS,     $json);        // cuerpo de la petición
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length: " . strlen($json)
]);
$respuesta  = curl_exec($ch);
$codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
```

---

### 3.4 POST con XML (para SOAP)

```php
$envelopeXml = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <consultarStock>
      <nombre>Camiseta</nombre>
    </consultarStock>
  </soap:Body>
</soap:Envelope>';

$ch = curl_init($urlSoap);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     $envelopeXml);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: text/xml; charset=utf-8",   // SOAP siempre text/xml
    "SOAPAction: consultarStock",               // nombre de la operación
    "Content-Length: " . strlen($envelopeXml)
]);
$respuesta  = curl_exec($ch);
$codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Parsear la respuesta XML en PHP
$dom = new DOMDocument();
$dom->loadXML($respuesta);
$stock = $dom->getElementsByTagName("stock")->item(0)->nodeValue;
```

---

### 3.5 Tabla de opciones cURL más usadas

| Opción | Efecto |
|--------|--------|
| `CURLOPT_RETURNTRANSFER => true` | **Obligatorio** — captura la respuesta como string en lugar de imprimirla |
| `CURLOPT_POST => true` | Usa método POST |
| `CURLOPT_POSTFIELDS => $body` | Cuerpo del POST (string JSON o XML) |
| `CURLOPT_HTTPHEADER => [...]` | Array de cabeceras HTTP: `["Content-Type: application/json"]` |
| `CURLOPT_TIMEOUT => 10` | Tiempo máximo total de la petición (segundos) |
| `CURLOPT_CONNECTTIMEOUT => 5` | Tiempo máximo para establecer la conexión (segundos) |
| `CURLOPT_URL => $url` | URL (alternativa a pasarla en `curl_init`) |
| `CURLINFO_HTTP_CODE` | Constante para `curl_getinfo` — obtiene el status HTTP |

---

## 4. JavaScript — fetch() como cliente HTTP

### 4.1 GET — leer datos REST

```javascript
fetch("http://localhost/api.php/productos")
    .then(function(respuesta) {
        return respuesta.json();   // convierte el body JSON a objeto JS
    })
    .then(function(datos) {
        console.log(datos);        // array o objeto con los datos
    })
    .catch(function(error) {
        console.error("Error de red:", error);
    });
```

---

### 4.2 POST — crear un recurso REST

```javascript
const producto = { nombre: "Auriculares", precio: 29.99 };

fetch("http://localhost/api.php/productos", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"   // obligatorio en POST/PUT con JSON
    },
    body: JSON.stringify(producto)           // objeto JS → string JSON
})
.then(function(respuesta) {
    // leer código HTTP y body juntos
    return respuesta.json().then(function(datos) {
        return { codigo: respuesta.status, datos: datos };
    });
})
.then(function(resultado) {
    if (resultado.codigo === 201) {
        console.log("Creado:", resultado.datos);
    } else {
        console.log("Error:", resultado.datos.error);
    }
})
.catch(function(error) { console.error(error); });
```

---

### 4.3 DELETE — eliminar un recurso REST

```javascript
fetch("http://localhost/api.php/productos/3", { method: "DELETE" })
    .then(function(respuesta) {
        if (respuesta.status === 204) {
            // 204 = No Content: éxito pero sin body — NO llames a .json()
            console.log("Eliminado correctamente");
        } else {
            return respuesta.json().then(function(datos) {
                console.log("Error:", datos.error);
            });
        }
    });
```

---

### 4.4 Llamada SOAP con fetch (desde el navegador)

```javascript
const titulo = "Matrix";

// El envelope XML — siempre la misma estructura, solo cambia la operación y parámetros
const envelope = `<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <consultarStock>
      <nombre>${titulo}</nombre>
    </consultarStock>
  </soap:Body>
</soap:Envelope>`;

fetch("http://localhost/soap_servidor.php", {
    method: "POST",                              // SOAP siempre POST
    headers: {
        "Content-Type": "text/xml; charset=utf-8",  // SOAP siempre text/xml
        "SOAPAction": "consultarStock"              // nombre de la operación
    },
    body: envelope
})
.then(function(respuesta) {
    return respuesta.text();       // SOAP devuelve XML (string), NO .json()
})
.then(function(xmlText) {
    // Parsear el XML en el navegador
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(xmlText, "text/xml");

    // Comprobar si hay Fault antes de leer los datos
    const fault = xmlDoc.getElementsByTagName("faultstring")[0]?.textContent;
    if (fault) {
        console.error("SOAP Fault:", fault);
        return;
    }

    // Leer los valores de la respuesta
    const stock = xmlDoc.getElementsByTagName("stock")[0]?.textContent;
    const disp  = xmlDoc.getElementsByTagName("disponible")[0]?.textContent;
    console.log("Stock:", stock, "| Disponible:", disp);
})
.catch(function(error) { console.error(error); });
```

---

### 4.5 Fragmentos HTML dinámicos (fetch + innerHTML)

```javascript
// El servidor devuelve HTML ya formateado → se inyecta directamente en el DOM
fetch("servidor/fragmento.php?categoria=Accion")
    .then(function(respuesta) {
        return respuesta.text();   // HTML es texto plano, no JSON
    })
    .then(function(html) {
        document.getElementById("contenedor").innerHTML = html;  // inyecta el HTML
    });
```

---

### 4.6 Diferencias clave entre .json() y .text()

| Método | Úsalo cuando | El servidor devuelve |
|--------|-------------|----------------------|
| `respuesta.json()` | REST | `Content-Type: application/json` |
| `respuesta.text()` | SOAP, fragmentos HTML | `Content-Type: text/xml` o `text/html` |

---

## 5. PHP con PDO — base de datos

### 5.1 Conexión

```php
$pdo = new PDO(
    "mysql:host=localhost;dbname=mi_bd;charset=utf8",
    "usuario",
    "contraseña",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]  // lanza excepciones en errores
);
```

---

### 5.2 Consulta SELECT

```php
// Sin parámetros
$stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre ASC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);   // array de arrays asociativos

// Con parámetros (consulta preparada — evita SQL injection)
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->execute([":id" => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);       // una sola fila
```

---

### 5.3 INSERT / UPDATE / DELETE

```php
$stmt = $pdo->prepare(
    "INSERT INTO productos (nombre, precio) VALUES (:nombre, :precio)"
);
$stmt->execute([":nombre" => $nombre, ":precio" => $precio]);
$nuevoId = $pdo->lastInsertId();   // id del registro recién creado

$stmt = $pdo->prepare("UPDATE productos SET precio = :precio WHERE id = :id");
$stmt->execute([":precio" => $precio, ":id" => $id]);

$stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
$stmt->execute([":id" => $id]);
$filasAfectadas = $stmt->rowCount();  // 0 si no existía, 1 si se eliminó
```

---

### 5.4 Transacciones

```php
try {
    $pdo->beginTransaction();
    $pdo->prepare("INSERT ...")->execute([...]);
    $pdo->prepare("UPDATE ...")->execute([...]);
    $pdo->commit();           // confirma los dos cambios a la vez
} catch (PDOException $e) {
    $pdo->rollBack();         // deshace todo si algo falla
    responder(500, ["error" => $e->getMessage()]);
}
```

---

## 6. Funciones PHP más usadas en el tema

| Función | Qué hace | Ejemplo |
|---------|----------|---------|
| `header($cabecera)` | Fija una cabecera HTTP de respuesta | `header("Content-Type: application/json")` |
| `http_response_code($n)` | Fija el código de status HTTP | `http_response_code(404)` |
| `json_encode($array, $flags)` | Array PHP → string JSON | `json_encode($datos, JSON_UNESCAPED_UNICODE)` |
| `json_decode($string, true)` | String JSON → array PHP | `json_decode($raw, true)` |
| `file_get_contents($ruta)` | Lee un archivo o `php://input` como string | `file_get_contents("php://input")` |
| `file_put_contents($ruta, $str)` | Escribe un string en un archivo | `file_put_contents("datos.json", $json)` |
| `array_column($arr, $clave)` | Extrae una columna del array | `array_column($items, "id")` → `[1,2,3]` |
| `array_splice($arr, $i, $n)` | Elimina elementos del array | `array_splice($items, $indice, 1)` |
| `htmlspecialchars($str, ENT_XML1)` | Escapa caracteres especiales para XML | Usar siempre en valores dentro de XML |
| `trim($str)` | Elimina espacios y saltos de línea | `trim($nodo->nodeValue)` |
| `strtolower($str)` | Convierte a minúsculas | Para comparar sin distinguir mayúsculas |

---

## 7. Resumen de diferencias REST vs SOAP

| | REST | SOAP |
|--|------|------|
| Formato | **JSON** | **XML** (con envelope) |
| Método HTTP | GET / POST / PUT / DELETE | Siempre **POST** |
| Content-Type (servidor) | `application/json` | `text/xml; charset=utf-8` |
| Content-Type (cliente fetch) | `application/json` | `text/xml; charset=utf-8` |
| Cabecera extra | — | `SOAPAction: nombreOperacion` |
| Errores | Status codes HTTP (400, 404...) | **SOAP Fault** dentro del XML |
| Parsear respuesta en JS | `respuesta.json()` | `respuesta.text()` + `DOMParser` |
| Parsear respuesta en PHP | `json_decode($str, true)` | `new DOMDocument()` + `loadXML()` |
| Contrato del servicio | OpenAPI / YAML | **WSDL** (XML) |
| Cuándo usarlo | APIs públicas, apps web | Servicios empresariales, legados |
