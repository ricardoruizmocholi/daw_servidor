# 009 — Modificación de la estructura y contenido

## ¿Qué se practica aquí?
Aquí el servidor genera **fragmentos de HTML** (no páginas completas) que el cliente inserta directamente en el DOM. Es como pedir al restaurante solo la guarnición, no el plato completo: el cliente sabe dónde colocarla, el servidor decide cómo prepararla. Este patrón permite cambiar partes de la página dinámicamente (filtros, vistas, ordenación) sin recargar nada.

## Archivos

| Archivo | Propósito |
|---|---|
| `cliente.html` | Interfaz con filtros (género, orden, tipo de vista) y un `div` donde se inyecta el fragmento HTML |
| `servidor/catalogoFragmentos.php` | Genera fragmentos HTML (tarjetas **o** tabla) según los filtros recibidos por GET |
| `servidor/videojuegos.json` | Catálogo de videojuegos en JSON — la "base de datos" del servidor |

## Conceptos clave
- **SSR parcial (fragmentos)**: el servidor genera HTML, no el cliente — el cliente solo hace `innerHTML = fragmento`
- **`response.text()` en vez de `response.json()`**: cuando el servidor devuelve HTML, el cliente lo lee como texto, no como JSON
- **`usort()` en PHP**: ordena arrays por el valor de un campo concreto para implementar la ordenación en servidor
- **Filtros por GET**: los parámetros de filtro viajan en la URL como `?genero=accion&orden=precio&vista=tarjetas`
- **Dos vistas**: tarjetas (cards) y tabla — mismos datos, diferente presentación HTML

## Diagrama del flujo de fragmentos

```
CLIENTE (cliente.html)                SERVIDOR (catalogoFragmentos.php)
         │                                       │
         │  Usuario cambia filtro de vista        │
         │                                       │
         │  fetch("catalogoFragmentos.php         │
         │    ?genero=accion                      │
         │    &orden=precio                ─────►│ Lee videojuegos.json
         │    &vista=tarjetas")                   │ Filtra por género
         │                                       │ Ordena por precio (usort)
         │                                       │ Genera HTML de tarjetas
         │                                       │
         │  ◄── 200 OK + HTML (tarjetas)    ─── │
         │                                       │
         │  divCatalogo.innerHTML = fragmento     │
         │  (el div se actualiza sin recargar)   │
```

## Diferencia entre JSON y fragmentos HTML

```
API REST (devuelve JSON → cliente genera HTML):
  Servidor → { "nombre": "Zelda", "precio": 59.99 }
  Cliente  → crea <div class="tarjeta">Zelda — 59.99€</div>

Fragmentos SSR (devuelve HTML directamente):
  Servidor → <div class="tarjeta"><h3>Zelda</h3><p>59.99€</p></div>
  Cliente  → contenedor.innerHTML = respuestaDelServidor
```

## Cuándo usar cada enfoque

| Enfoque | Cuándo usarlo |
|---|---|
| **JSON + JS genera HTML** | Cuando el cliente necesita los datos para lógica propia (filtrar, ordenar, guardar) |
| **Fragmentos HTML del servidor** | Cuando el servidor ya sabe exactamente qué HTML mostrar — más simple para vistas complejas |

## Para el examen
⚡ Cuando el servidor devuelve **HTML** (no JSON), el cliente usa `response.text()` en lugar de `response.json()` — si usas `response.json()` con HTML recibirás un error de parseo.

⚡ `innerHTML = fragmentoHTMLDelServidor` es la forma de inyectar HTML generado en servidor dentro de un `div` ya existente, sin recargar la página.

⚡ La diferencia entre **SSR completo** (página entera generada en servidor) y **fragmentos** es que los fragmentos se insertan en un `div` ya existente — el resto de la página no cambia.
