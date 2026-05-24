# 007 — Generación dinámica de páginas interactivas

## ¿Qué se practica aquí?
Aquí construyes un sistema donde el **cliente HTML no recarga la página**, sino que hace peticiones silenciosas a servicios PHP y actualiza partes del DOM con los datos recibidos. Es como un camarero eficiente que va a la cocina solo por lo que necesita en cada momento, sin traer toda la carta ni poner y quitar todo el menú cada vez. El servidor devuelve **solo datos (JSON)**, nunca HTML — el cliente es quien construye la presentación.

## Archivos

| Archivo | Propósito |
|---|---|
| `cliente.html` | Interfaz con pestañas de videojuegos y alumnos; usa `fetch()` para llamar al controlador |
| `controlador.php` | **Dispatcher** (Front Controller): recibe TODAS las peticiones y las redirige al servicio correcto |
| `servicios/servicioVideojuegos.php` | Servicio GET/POST de videojuegos: lee y escribe en `datos/videojuegos.json` |
| `servicios/servicioAlumnos.php` | Servicio GET/POST de alumnos: lee y escribe en `datos/alumnos.json` |
| `datos/videojuegos.json` | Almacenamiento de datos de videojuegos |
| `datos/alumnos.json` | Almacenamiento de datos de alumnos |

## Conceptos clave
- **Patrón Front Controller**: una sola entrada (`controlador.php`), múltiples servicios detrás — base de todos los frameworks modernos (Laravel, Symfony, Express)
- **`fetch()` asíncrono**: con `await` para esperar la respuesta sin bloquear el navegador
- **DOM dinámico**: `innerHTML`, `createElement`, `appendChild` para construir HTML en JavaScript
- **Separación de responsabilidades**: servidor devuelve JSON → cliente genera el HTML a partir de esos datos
- **JSON como almacenamiento**: `file_get_contents` + `json_decode` + `file_put_contents` + `json_encode`

## Diagrama del patrón Front Controller

```
CLIENTE (cliente.html)
         │
         │  fetch("controlador.php?servicio=videojuegos")
         │
         ▼
CONTROLADOR (controlador.php)  ─────────────────────────────┐
         │                                                    │
         │  switch($_GET["servicio"])                         │
         │                                                    │
         ├── "videojuegos" ──► servicios/servicioVideojuegos.php
         │                              │ lee/escribe datos/videojuegos.json
         │                              │ devuelve JSON
         │
         └── "alumnos"    ──► servicios/servicioAlumnos.php
                                       │ lee/escribe datos/alumnos.json
                                       │ devuelve JSON
```

## Flujo completo de una petición

```
1. Usuario hace clic en "Ver videojuegos"
2. JS llama: fetch("controlador.php?servicio=videojuegos")
3. controlador.php detecta servicio=videojuegos, incluye servicioVideojuegos.php
4. servicioVideojuegos.php lee videojuegos.json y devuelve JSON
5. JS recibe el JSON y genera filas de tabla con createElement
6. El DOM se actualiza — sin recargar la página
```

## Para el examen
⚡ El patrón **Front Controller** (`controlador.php`) es la base de cualquier framework web moderno: todas las peticiones entran por un único punto, que las enruta al servicio correspondiente.

⚡ `fetch()` devuelve una **Promesa** — siempre debes usar `.then()` o `await` para acceder a los datos. Nunca puedes usarlos directamente después del `fetch()` sin esperar.

⚡ La separación **datos (JSON) / lógica (PHP) / presentación (HTML+JS)** es el pilar de la arquitectura en capas — si cambias el diseño visual, no tocas el PHP; si cambias los datos, no tocas el HTML.
