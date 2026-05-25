# Ejercicios de Preparación — Trimestral 3ª Evaluación

## Cómo usar esta carpeta

Estos ejercicios están ordenados de **0% a 100% de dificultad**. Empieza desde el nivel 1 aunque te parezca fácil — los errores conceptuales en los fundamentos son los que más puntos hacen perder en el examen.

Cada ejercicio tiene:
- **TODO** marcados con el número → completa solo eso
- **Pistas** cuando el concepto es nuevo o difícil
- **Para el examen** → el punto concreto que suele caer

---

## Mapa de ejercicios

### Nivel 1 — Básico (0–25%) · ~30 min
> Si no dominas esto, empieza aquí sin excusas.

| Ejercicio | Qué entrena |
|---|---|
| `ej01_teoria_preguntas.md` | Preguntas de teoría tipo examen — REST vs SOAP, status codes, WSDL |
| `ej02_status_codes.md` | Completar la tabla de status codes con escenarios reales |
| `ej03_json_simple.php` | PHP mínimo: `header()` + `json_encode()` |
| `ej04_envelope_soap.md` | Completar un XML SOAP con los huecos correctos |

### Nivel 2 — Intermedio (25–50%) · ~45 min
> La mecánica básica de servicios REST y SOAP.

| Ejercicio | Qué entrena |
|---|---|
| `ej05_rest_get.php` | Endpoint REST GET con filtro por parámetro GET |
| `ej06_soap_simple.php` | Servicio SOAP que parsea XML y responde con XML |
| `ej07_pdo_consulta.php` | Conexión PDO + consulta preparada + JSON de respuesta |
| `ej08_fetch_cliente.html` | Cliente HTML con `fetch()` + renderizado del DOM |

### Nivel 3 — Avanzado (50–75%) · ~60 min
> Servicios completos tal como los pide el examen.

| Ejercicio | Qué entrena |
|---|---|
| `ej09_rest_crud.php` | API REST completa CRUD con fichero JSON |
| `ej10_soap_validacion.php` | SOAP con validación robusta y Fault para errores |
| `ej11_wsdl_diseno.md` | Diseñar un WSDL desde cero a partir de un enunciado |
| `ej12_cliente_soap.html` | Cliente SOAP completo con JS — petición y parseo XML |

### Nivel 4 — Experto (75–100%) · ~60 min
> Lo que te diferencia de un aprobado a un sobresaliente.

| Ejercicio | Qué entrena |
|---|---|
| `ej13_soap_con_header.php` | SOAP con Header de sesión — leer y validar el token |
| `ej14_curl_proxy.php` | cURL como intermediario — el servidor llama a otro servidor |
| `ej15_fragmentos_dinamicos.php` | Fragmentos HTML generados en servidor con filtros |
| `ej16_simulacro_examen.md` | **Simulacro completo** — caso real de examen cronometrado |

---

## Checklist de conceptos para el examen

Marca los que ya controlas:

### REST
- [ ] `header("Content-Type: application/json")` — siempre al principio
- [ ] `$_SERVER["REQUEST_METHOD"]` — cómo saber el método HTTP
- [ ] `$_SERVER["PATH_INFO"]` — cómo leer el ID de la URL
- [ ] `file_get_contents("php://input")` — cómo leer el body JSON
- [ ] `json_decode($raw, true)` — string JSON → array PHP
- [ ] `json_encode($datos)` — array PHP → string JSON
- [ ] Status codes: 200, 201, 204, 400, 404, 405

### SOAP
- [ ] Estructura Envelope → Header (opcional) → Body (obligatorio)
- [ ] `DOMDocument::loadXML()` — parsear el XML recibido
- [ ] `DOMXPath::registerNamespace()` — registrar el namespace `soap:`
- [ ] `getElementsByTagName("nombre")->item(0)->textContent` — leer un parámetro
- [ ] Construir `<soap:Envelope>` manualmente como string PHP
- [ ] SOAP Fault → `faultcode` + `faultstring`
- [ ] WSDL: types → message → portType → binding → service

### PHP general
- [ ] `PDO` + `prepare()` + `execute()` + `fetchAll(PDO::FETCH_ASSOC)`
- [ ] `beginTransaction()` / `commit()` / `rollBack()`
- [ ] `file_get_contents()` + `file_put_contents()` — leer/escribir ficheros
- [ ] `curl_init()` → `curl_setopt()` → `curl_exec()` → `curl_close()`

### JavaScript cliente
- [ ] `fetch(url, { method, headers, body })` + `await`
- [ ] `response.json()` para REST · `response.text()` para SOAP/fragmentos
- [ ] `DOMParser().parseFromString(texto, "text/xml")` — parsear XML en JS
- [ ] `innerHTML` para inyectar HTML recibido del servidor
