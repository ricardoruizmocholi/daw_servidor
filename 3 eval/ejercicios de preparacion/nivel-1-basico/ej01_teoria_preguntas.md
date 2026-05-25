# EJ01 — Teoría: Preguntas tipo examen

Responde **sin mirar** los apuntes. Luego comprueba tus respuestas abajo.
Tiempo estimado: 15 minutos.

---

## Bloque A — REST vs SOAP

**1.** ¿Cuál es la diferencia principal entre REST y SOAP?

_Tu respuesta:_
```

Rest usa HTTP derectamente (URLs + metodos HTTP + JSON), es flexible sin contrato formal. Mientras que SOUP usa xml con un contrato obligatorio llamado WSDL. SOAP siempre usa POST y REST usa el 
metodo HTTP correcto para cada operacion
```

**2.** En REST, ¿qué método HTTP usarías para cada situación?
| Situación | Método |
|---|---|
| Obtener la lista | **GET** |
| Crear usuario nuevo | **POST** |
| Actualizar solo el email | **PATCH** |
| Reemplazar un usuario completo | **PUT** |
| Eliminar | **DELETE** |

**3.** En SOAP, el "verbo" de la operación (sumar, calcularEnvio...) ¿en qué parte del mensaje va?

_Tu respuesta:_
```
En SOAP el verbo va dentro del **Body XML**, como nombre del elemento hijo: `<sumar>`, `<calcularEnvio>`, etc. Nunca en la URL.

```

**4.** ¿Qué es el WSDL y para qué sirve?

_Tu respuesta:_
```
Es el contarto un documento que escipula que el lo que hace el servicio 

El WSDL (Web Services Description Language) es el **contrato** del servicio SOAP. Define qué operaciones existen, qué parámetros reciben, qué devuelven y en qué URL está el servicio.
```

**5.** ¿Qué diferencia hay entre `SOAP Header` y `SOAP Body`?

_Tu respuesta:_
```
El **Header** transporta **metadatos** de la llamada: tokens de sesión, IDs de trazabilidad, timestamps. El **Body** contiene la **operación y sus datos de negocio**. Header es opcional; Body es obligatorio.

```

---

## Bloque B — Status codes

**6.** ¿Qué status code devuelves en cada situación?

| Situación | Status code |
|---|---|
| GET /usuarios — todo OK, devuelves los datos |200 |
| POST /usuarios — se creó el usuario correctamente |201 |
| DELETE /usuarios/5 — se eliminó correctamente | |
| GET /usuarios/99 — no existe ese ID | |
| POST /usuarios — el body tiene campos que faltan | |
| PUT /usuarios — método no soportado en esa ruta | |
| El servidor ha petado por un bug interno | |


| Situación | Status code |
|---|---|
| GET exitoso | **200 OK** |
| POST exitoso (creación) | **201 Created** |
| DELETE exitoso | **204 No Content** |
| ID no existe | **404 Not Found** |
| Campos que faltan | **400 Bad Request** |
| Método no soportado | **405 Method Not Allowed** |
| Bug interno del servidor | **500 Internal Server Error** |

---

## Bloque C — PHP y protocolos

**7.** ¿Qué hace esta línea y por qué es necesaria en un servicio REST?
```php
header("Content-Type: application/json; charset=utf-8");
```
_Tu respuesta:_
```
Le dice al cliente (navegador, Postman, etc.) que la respuesta es JSON. Sin este header, el cliente no sabe interpretar los datos correctamente y puede tratarlos como texto plano.
Le dice que lo que esta buscando para intrerpretar en un json
header("Content-Type: application/json; charset=utf-8)
```

**8.** ¿Cuál es la diferencia entre `$_POST["campo"]` y `file_get_contents("php://input")`?

_Tu respuesta:_
```
`$_POST["campo"]` solo funciona cuando el `Content-Type` es `application/x-www-form-urlencoded` o `multipart/form-data` (formularios HTML clásicos). `file_get_contents("php://input")` lee el body **tal cual** — necesario para JSON (REST) y XML (SOAP), donde el Content-Type es `application/json` o `text/xml`.
```

**9.** ¿Qué hace `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`?

_Tu respuesta:_
```
Hace que los errores de la base de datos lancen **excepciones PHP** (`PDOException`) en lugar de pasar en silencio. Sin esto, las consultas fallidas no detienen el script y puedes estar devolviendo datos incorrectos sin darte cuenta.
```

**10.** ¿Qué hace `CURLOPT_RETURNTRANSFER => true` en cURL? ¿Qué pasa si no lo pones?

_Tu respuesta:_
```
 Sin `CURLOPT_RETURNTRANSFER => true`, cURL **imprime** directamente la respuesta en la página. Con `true`, la **guarda en una variable** que puedes procesar. Casi siempre lo necesitas.
```

**11.** ¿Cuándo usarías `response.json()` y cuándo `response.text()` después de un `fetch()`?

_Tu respuesta:_
```
`response.json()` cuando el servidor devuelve **JSON** (API REST). `response.text()` cuando devuelve **XML** (SOAP) o **HTML** (fragmentos SSR). Si usas `json()` con XML, obtendrás un error de parseo.
```

**12.** ¿Qué problema resuelve CORS y cómo lo solucionas desde el servidor PHP?

_Tu respuesta:_
```
CORS bloquea peticiones de un dominio a otro desde el navegador. Se resuelve **en el servidor** añadiendo el header: `header("Access-Control-Allow-Origin: *");` (o el dominio concreto). El cliente no puede resolverlo — es el servidor el que debe autorizar.

```

---

## Bloque D — WSDL y SOAP avanzado

**13.** Ordena las secciones del WSDL en el orden correcto:
`portType` / `binding` / `message` / `service` / `types`

_Tu respuesta:_
```
1. types
2. message
3. portType
4. binding
5. service
```

**14.** ¿Cuándo debes responder con un SOAP Fault y cuándo con una respuesta normal negativa?

_Tu respuesta:_
```
**SOAP Fault** → los datos de entrada son inválidos (formato incorrecto, campo vacío, método no POST) o hay un error de protocolo. **Respuesta normal negativa** → los datos son válidos pero el resultado de negocio es negativo (alumno no autorizado, libro no disponible). El Fault dice "algo está mal con la petición"; la respuesta negativa dice "la petición fue correcta pero el resultado es NO".
```

**15.** ¿Qué significa "contract-first" en el contexto de SOAP?

_Tu respuesta:_
```
"Contract-first" significa que **primero diseñas el WSDL** (el contrato), y luego implementas el servidor PHP que lo cumple. El WSDL actúa como interfaz que tanto el servidor como el cliente respetan. Es el opuesto de "code-first" (escribir el código y generar el WSDL automáticamente).

```

---

---
---

# RESPUESTAS

> No leas esto hasta haber intentado todas las preguntas.

---

**1. REST vs SOAP:**
REST es un estilo arquitectónico que usa HTTP directamente (URLs + métodos HTTP + JSON). Es flexible, sin contrato formal. SOAP es un protocolo estricto basado en XML con un contrato obligatorio (WSDL). SOAP siempre usa POST; REST usa el método HTTP correcto para cada operación.

**2. Métodos HTTP:**

| Situación | Método |
|---|---|
| Obtener la lista | **GET** |
| Crear usuario nuevo | **POST** |
| Actualizar solo el email | **PATCH** |
| Reemplazar un usuario completo | **PUT** |
| Eliminar | **DELETE** |

**3.** En SOAP el verbo va dentro del **Body XML**, como nombre del elemento hijo: `<sumar>`, `<calcularEnvio>`, etc. Nunca en la URL.

**4.** El WSDL (Web Services Description Language) es el **contrato** del servicio SOAP. Define qué operaciones existen, qué parámetros reciben, qué devuelven y en qué URL está el servicio. Es como la carta oficial del restaurante — antes de pedir, consultas qué hay disponible y cómo pedirlo.

**5.** El **Header** transporta **metadatos** de la llamada: tokens de sesión, IDs de trazabilidad, timestamps. El **Body** contiene la **operación y sus datos de negocio**. Header es opcional; Body es obligatorio.

**6. Status codes:**

| Situación | Status code |
|---|---|
| GET exitoso | **200 OK** |
| POST exitoso (creación) | **201 Created** |
| DELETE exitoso | **204 No Content** |
| ID no existe | **404 Not Found** |
| Campos que faltan | **400 Bad Request** |
| Método no soportado | **405 Method Not Allowed** |
| Bug interno del servidor | **500 Internal Server Error** |

**7.** Le dice al cliente (navegador, Postman, etc.) que la respuesta es JSON. Sin este header, el cliente no sabe interpretar los datos correctamente y puede tratarlos como texto plano.

**8.** `$_POST["campo"]` solo funciona cuando el `Content-Type` es `application/x-www-form-urlencoded` o `multipart/form-data` (formularios HTML clásicos). `file_get_contents("php://input")` lee el body **tal cual** — necesario para JSON (REST) y XML (SOAP), donde el Content-Type es `application/json` o `text/xml`.

**9.** Hace que los errores de la base de datos lancen **excepciones PHP** (`PDOException`) en lugar de pasar en silencio. Sin esto, las consultas fallidas no detienen el script y puedes estar devolviendo datos incorrectos sin darte cuenta.

**10.** Sin `CURLOPT_RETURNTRANSFER => true`, cURL **imprime** directamente la respuesta en la página. Con `true`, la **guarda en una variable** que puedes procesar. Casi siempre lo necesitas.

**11.** `response.json()` cuando el servidor devuelve **JSON** (API REST). `response.text()` cuando devuelve **XML** (SOAP) o **HTML** (fragmentos SSR). Si usas `json()` con XML, obtendrás un error de parseo.

**12.** CORS bloquea peticiones de un dominio a otro desde el navegador. Se resuelve **en el servidor** añadiendo el header: `header("Access-Control-Allow-Origin: *");` (o el dominio concreto). El cliente no puede resolverlo — es el servidor el que debe autorizar.

**13. Orden del WSDL:**
```
1. types
2. message
3. portType
4. binding
5. service
```

**14.** **SOAP Fault** → los datos de entrada son inválidos (formato incorrecto, campo vacío, método no POST) o hay un error de protocolo. **Respuesta normal negativa** → los datos son válidos pero el resultado de negocio es negativo (alumno no autorizado, libro no disponible). El Fault dice "algo está mal con la petición"; la respuesta negativa dice "la petición fue correcta pero el resultado es NO".

**15.** "Contract-first" significa que **primero diseñas el WSDL** (el contrato), y luego implementas el servidor PHP que lo cumple. El WSDL actúa como interfaz que tanto el servidor como el cliente respetan. Es el opuesto de "code-first" (escribir el código y generar el WSDL automáticamente).
