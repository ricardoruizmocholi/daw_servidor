# 005 — Consumo de un servicio web

## ¿Qué se practica aquí?
Aquí eres el **cliente** que consume APIs REST reales (aunque las construyas tú mismo). También aprendes a **documentar tu API con OpenAPI/Swagger**, el estándar de la industria que genera documentación interactiva automáticamente. Es como aprender a leer la carta oficial del restaurante (OpenAPI) y a hacer pedidos completos: consultar la carta, pedir un plato nuevo, cambiar un ingrediente y cancelar un pedido — eso es el CRUD completo.

## Archivos

| Archivo | Propósito |
|---|---|
| `apiRestLibros.php` | API REST completa de libros: GET/POST/PATCH/DELETE sobre `/libros` y `/libros/{id}` |
| `apiRestLibros.html` | Cliente REST de libros con todas las operaciones CRUD |
| `libros.json` | Almacenamiento de datos de libros (simula una BD con un fichero JSON) |
| `openApiLibros.yaml` | Especificación OpenAPI 3.0 del servicio de libros |
| `apiVideojuegos.php` | API REST de videojuegos con MySQL y filtros por precio/género |
| `apiVideojuegos.html` | Cliente REST de videojuegos con filtros y CRUD |
| `openApiVideojuegos.yaml` | Especificación OpenAPI 3.0 del servicio de videojuegos |
| `openApiEstudios.yaml` | Especificación OpenAPI 3.0 del servicio de estudios de juegos |
| `gestorTareas.txt` | Enunciado: crear API REST y cliente de gestión de tareas |
| `gestorTareas.php` | API REST de tareas: CRUD completo (id, título, descripción, prioridad, completada, fecha) |
| `gestorTareas.html` | Cliente REST del gestor de tareas con formularios de creación y edición |

## Conceptos clave
- **REST CRUD completo**: GET (leer), POST (crear), PATCH (actualizar parcialmente), DELETE (eliminar)
- **`PATH_INFO`**: cómo PHP lee el ID de la URL — si la URL es `/api.php/libros/3`, `PATH_INFO` es `/libros/3`
- **`php://input`**: cómo leer el body JSON que envía el cliente en POST/PATCH
- **Status codes semánticos**: 200 OK, 201 Created, 204 No Content, 400 Bad Request, 404 Not Found, 405 Method Not Allowed
- **JSON file storage**: usar `file_get_contents` + `json_decode` + `file_put_contents` + `json_encode` como BD sencilla
- **OpenAPI 3.0**: `paths`, `components/schemas`, `responses`, `requestBody`

## Tabla de operaciones REST

| Método | URL | Descripción | Status OK |
|---|---|---|---|
| GET | `/tareas` | Devuelve todas las tareas | 200 |
| GET | `/tareas/5` | Devuelve la tarea con id=5 | 200 |
| POST | `/tareas` | Crea una tarea nueva | 201 |
| PATCH | `/tareas/5` | Actualiza campos concretos de la tarea 5 | 200 |
| DELETE | `/tareas/5` | Elimina la tarea 5 | 204 |

## Diagrama de flujo REST

```
CLIENTE (gestorTareas.html)              SERVIDOR (gestorTareas.php)
         │                                         │
         │  GET /gestorTareas.php/tareas    ─────►│ Lee tareas.json
         │  ◄──── 200 OK + JSON array        ─── │
         │                                         │
         │  POST /gestorTareas.php/tareas          │
         │  Body: {"titulo":"Estudiar",…}  ─────►│ Añade al JSON, guarda, genera ID
         │  ◄──── 201 Created + JSON tarea   ─── │
         │                                         │
         │  DELETE /gestorTareas.php/tareas/3      │
         │  ◄──── 204 No Content              ─── │ (sin body)
```

## Para el examen
⚡ El status **201 Created** es para POST exitoso (creación). El **200 OK** es para GET o PATCH exitoso. El **204 No Content** es para DELETE exitoso — sin cuerpo de respuesta.

⚡ **`PATH_INFO`** te da la parte de la URL después del nombre del script. Si la URL es `/gestorTareas.php/tareas/3`, `PATH_INFO` es `/tareas/3`. Si no hay nada después, `PATH_INFO` está vacío o no existe.

⚡ **PATCH** actualiza solo los campos que envías; **PUT** reemplaza el recurso entero. En el examen, si solo cambias un campo, usa PATCH.
