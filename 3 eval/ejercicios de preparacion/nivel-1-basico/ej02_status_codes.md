# EJ02 — Status Codes: Completa la tabla y los escenarios

Tiempo estimado: 10 minutos.

---

## Parte A — Completa la descripción

Escribe qué significa cada status code **y en qué situación REST lo usarías**:

| Código | Nombre | Cuándo lo usas en REST |
|---|---|---|
| 200 | **OK** | GET exitoso, PATCH exitoso |
| 201 | **Created** | POST exitoso (recurso creado) |
| 204 | **No Content** | DELETE exitoso (sin body de respuesta) |
| 400 | **Bad Request** | Datos mal formados, campos obligatorios que faltan |
| 401 | **Unauthorized** | No autenticado — "¿quién eres?" |
| 403 | **Forbidden** | Autenticado pero sin permisos — "no puedes hacer eso" |
| 404 | **Not Found** | El ID no existe o la ruta no existe |
| 405 | **Method Not Allowed** | El método HTTP no está soportado para esa ruta |
| 422 | **Unprocessable Entity** | Los datos llegan pero son semánticamente inválidos (email con formato incorrecto, etc.) |
| 500 | **Internal Server Error** | Error del servidor — bug, excepción no capturada |
| 503 | **Service Unavailable** | El servidor está caído o sobrecargado |

---

## Parte B — Escenarios: ¿qué status code devuelves?

Lee cada situación y escribe el código correcto:

**1.** El cliente hace `GET /libros` y hay 3 libros en la base de datos.
→ Status: `200` — GET exitoso

**2.** El cliente hace `POST /libros` con `{"titulo": "El Quijote", "autor": "Cervantes"}` y se crea correctamente.
→ Status: 201

**3.** El cliente hace `DELETE /libros/7` y el libro con id=7 se elimina.
→ Status: 204

**4.** El cliente hace `GET /libros/99` pero no existe ningún libro con id=99.
→ Status: 404

**5.** El cliente hace `POST /libros` pero olvida enviar el campo `autor`.
→ Status: 400

**6.** El cliente hace `PUT /libros` pero tu API solo acepta `GET` y `POST` en `/libros`.
→ Status: 405

**7.** Hay un error de PHP en el servidor que hace que el script explote.
→ Status: 500

**8.** El cliente hace `PATCH /libros/3` con `{"disponible": true}` y se actualiza correctamente.
→ Status: 200

**9.** El cliente necesita autenticarse pero no ha enviado ningún token.
→ Status: 401

**10.** El cliente tiene token válido pero no tiene permisos de administrador para esa ruta.
→ Status: 403

---

## Parte C — Código PHP: ¿qué status code falta?

Completa el `___` en cada fragmento:

```php
// Recurso creado exitosamente
http_response_code(200);
echo json_encode($nuevoLibro);
```

```php
// Recurso no encontrado
http_response_code(404);
echo json_encode(["error" => "Libro no encontrado"]);
```

```php
// Eliminado correctamente — sin cuerpo de respuesta
http_response_code(204);
// (no echo aquí — 204 no lleva body)
```

```php
// Método HTTP no permitido
http_response_code(405);
echo json_encode(["error" => "Método no soportado"]);
```

---

---
---

# RESPUESTAS

> No leas esto hasta haber intentado todo.

---

## Parte A

| Código | Nombre | Cuándo lo usas en REST |
|---|---|---|
| 200 | **OK** | GET exitoso, PATCH exitoso |
| 201 | **Created** | POST exitoso (recurso creado) |
| 204 | **No Content** | DELETE exitoso (sin body de respuesta) |
| 400 | **Bad Request** | Datos mal formados, campos obligatorios que faltan |
| 401 | **Unauthorized** | No autenticado — "¿quién eres?" |
| 403 | **Forbidden** | Autenticado pero sin permisos — "no puedes hacer eso" |
| 404 | **Not Found** | El ID no existe o la ruta no existe |
| 405 | **Method Not Allowed** | El método HTTP no está soportado para esa ruta |
| 422 | **Unprocessable Entity** | Los datos llegan pero son semánticamente inválidos (email con formato incorrecto, etc.) |
| 500 | **Internal Server Error** | Error del servidor — bug, excepción no capturada |
| 503 | **Service Unavailable** | El servidor está caído o sobrecargado |

## Parte B

1. `200` — GET exitoso
2. `201` — recurso creado
3. `204` — eliminado, sin cuerpo
4. `404` — no existe
5. `400` — datos incompletos
6. `405` — método no permitido
7. `500` — error interno del servidor
8. `200` — PATCH exitoso devuelve el recurso actualizado
9. `401` — sin autenticación
10. `403` — autenticado pero sin permisos

## Parte C

```php
http_response_code(201);  // Created
http_response_code(404);  // Not Found
http_response_code(204);  // No Content
http_response_code(405);  // Method Not Allowed
```

---

## Truco para recordarlos

```
2xx → Todo bien
  200 = OK (leer, actualizar)
  201 = Created (crear con POST)
  204 = No Content (borrar con DELETE)

4xx → Error del CLIENTE (tú pediste mal)
  400 = Bad Request (datos incorrectos)
  401 = Unauthorized (sin identificar)
  403 = Forbidden (sin permiso)
  404 = Not Found (no existe)
  405 = Method Not Allowed (método equivocado)

5xx → Error del SERVIDOR (la cocina explotó)
  500 = Internal Server Error
```
