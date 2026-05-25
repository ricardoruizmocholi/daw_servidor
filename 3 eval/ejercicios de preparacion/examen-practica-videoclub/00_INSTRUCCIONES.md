# Examen Práctico — Videoclub: SOAP + REST + cURL

## Tema
Gestión de una videoteca: consultar disponibilidad, alquilar y administrar el catálogo de películas.

## Archivos
| Archivo                  | Qué implementar                    | Tiempo estimado |
|--------------------------|------------------------------------|-----------------|
| `01_soap_servidor.php`   | Servidor SOAP con 2 operaciones    | 25 min          |
| `02_rest_servidor.php`   | API REST CRUD completa (5 rutas)   | 35 min          |
| `03_curl_cliente.php`    | Cliente cURL (3 llamadas)          | 15 min          |
| `04_frontend.html`       | Frontend fetch: REST + SOAP        | 20 min          |

## Configuración local (XAMPP)
Copia la carpeta a `C:/xampp/htdocs/videoclub/`
- SOAP servidor:  `http://localhost/videoclub/01_soap_servidor.php`
- REST servidor:  `http://localhost/videoclub/02_rest_servidor.php`
- Cliente cURL:   ejecuta `03_curl_cliente.php` desde consola o navegador
- Frontend:       abre `04_frontend.html` directamente en el navegador

## Rutas REST implementadas
```
GET    /02_rest_servidor.php/peliculas         → 200, todas las películas
GET    /02_rest_servidor.php/peliculas/{id}    → 200 | 404
POST   /02_rest_servidor.php/peliculas         → 201 | 400
PUT    /02_rest_servidor.php/peliculas/{id}    → 200 | 400 | 404
DELETE /02_rest_servidor.php/peliculas/{id}    → 204 | 404
```

## Operaciones SOAP implementadas
```
consultarDisponibilidad(titulo) → copias + precio_alquiler
alquilarPelicula(titulo)        → confirmacion + copias_restantes
```

## Puntos clave para el examen
- **SOAP** siempre es POST + `Content-Type: text/xml` + envelope XML
- **REST** devuelve JSON + `Content-Type: application/json`
- **DELETE** devuelve `204 No Content` (sin body — no llames a `.json()`)
- **POST** devuelve `201 Created` con el recurso nuevo
- **cURL** necesita `CURLOPT_RETURNTRANSFER => true` para capturar la respuesta como string
- El **envelope SOAP** tiene siempre: `<soap:Envelope>` → `<soap:Body>` → operación
- Para parsear XML en JavaScript: `new DOMParser().parseFromString(texto, "text/xml")`
- Para parsear XML en PHP: `new DOMDocument()` + `loadXML($xml)` + `getElementsByTagName()`
