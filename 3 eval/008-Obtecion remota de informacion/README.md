# 008 — Obtención remota de información

## ¿Qué se practica aquí?
Aquí el **servidor actúa como intermediario**: el cliente le pide datos, y el servidor los va a buscar a otro servidor externo usando **cURL**. Es como llamar al restaurante para preguntar si tienen un plato especial, y el restaurante llama a su proveedor para confirmarlo — tú no hablas con el proveedor, hablas con el restaurante. Este patrón también resuelve el problema de CORS de forma elegante.

## Archivos

| Archivo | Propósito |
|---|---|
| `cliente.html` | Interfaz con filtros de categoría; solicita datos al controlador del propio servidor |
| `servidor/controlador.php` | Usa **cURL** para llamar al proveedor externo y transforma el JSON recibido en HTML |
| `poveedorExterno/ofertas.php` | Simula un proveedor externo real: devuelve ofertas en JSON según la categoría pedida |

## Conceptos clave
- **cURL en PHP**: `curl_init()`, `curl_setopt()`, `curl_exec()`, `curl_close()` para hacer peticiones HTTP desde el servidor
- **Patrón intermediario (Proxy)**: el servidor recibe la petición del cliente y la reenvía a otro servicio
- **Solución a CORS**: el navegador solo habla con "tu" servidor; tu servidor habla con el externo sin restricciones de CORS
- **`CURLOPT_RETURNTRANSFER`**: opción crítica que hace que cURL devuelva la respuesta como string en lugar de imprimirla directamente
- **Transformación en servidor**: el PHP convierte el JSON del proveedor en HTML listo para mostrar

## Diagrama: el servidor como intermediario

```
NAVEGADOR (cliente.html)        SERVIDOR (controlador.php)     PROVEEDOR EXTERNO
         │                               │                        (ofertas.php)
         │  fetch("controlador.php       │                              │
         │    ?categoria=accion")  ─────►│                              │
         │                               │  cURL GET ofertas.php        │
         │                               │  ?categoria=accion    ──────►│
         │                               │                              │ busca ofertas
         │                               │  ◄── 200 OK + JSON      ─── │
         │                               │  json_decode()               │
         │                               │  genera HTML con foreach     │
         │  ◄── 200 OK + HTML fragment ──│                              │
         │  innerHTML = fragmento        │                              │
```

## Por qué cURL resuelve CORS

```
SIN intermediario (da error CORS):
  Navegador → fetch("http://proveedor-externo.com/api") → ❌ BLOQUEADO por CORS

CON intermediario (funciona):
  Navegador → fetch("mi-servidor.com/controlador.php") → ✅ mismo origen
  mi-servidor.com → cURL → proveedor-externo.com       → ✅ servidor a servidor, sin CORS
```

## Para el examen
⚡ **cURL** permite al servidor PHP hacer peticiones HTTP a otros servidores — es como un `fetch()` pero ejecutado en el servidor, no en el navegador.

⚡ `curl_setopt($ch, CURLOPT_RETURNTRANSFER, true)` es **esencial** — sin esta opción, cURL imprime la respuesta directamente en la página en lugar de devolverla como string que puedes procesar.

⚡ El patrón intermediario resuelve CORS porque el navegador solo habla con "tu" servidor (mismo origen), y las comunicaciones **servidor a servidor** no tienen restricción de CORS — CORS solo afecta al navegador.
