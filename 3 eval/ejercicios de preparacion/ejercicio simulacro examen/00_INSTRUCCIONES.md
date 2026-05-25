# Simulacro de Examen — Gestión de Tienda de Productos
**Duración estimada: 1 hora 30 minutos**

---

## Contexto del problema
Trabajas en el backend de una tienda online. Los productos están guardados en `productos.json`.
Tienes que implementar tres tecnologías distintas que se complementan.

---

## Estructura de un producto
```json
{
  "id": 1,
  "nombre": "Camiseta",
  "categoria": "Ropa",
  "precio": 19.99,
  "stock": 50
}
```

---

## PARTE 1 — Servicio SOAP `(30 minutos)` → archivos 01 y 02

Lee el WSDL `01_productos.wsdl` para entender qué operaciones debes implementar.
Luego completa los TODOs en `02_soap_servidor.php`.

| Operación | Entrada | Salida correcta | Error |
|-----------|---------|-----------------|-------|
| `consultarStock` | `<nombre>Camiseta</nombre>` | `<stock>50</stock><disponible>true</disponible>` | Fault si no existe |
| `obtenerPrecio` | `<nombre>Zapatos</nombre>` | `<nombre>Zapatos</nombre><precio>49.99</precio>` | Fault si no existe |

**Ruta del servidor:** `http://localhost/examen/02_soap_servidor.php`

---

## PARTE 2 — API REST `(35 minutos)` → archivos 03 y 04

Lee la especificación `03_rest_spec.yaml` y completa los TODOs en `04_rest_servidor.php`.

| Método | Ruta | Qué hace | Código |
|--------|------|----------|--------|
| GET | `/productos` | Lista todos | 200 |
| GET | `/productos/{id}` | Uno por id | 200 / 404 |
| POST | `/productos` | Crea nuevo | 201 / 400 |
| PUT | `/productos/{id}` | Actualiza completo | 200 / 400 / 404 |
| DELETE | `/productos/{id}` | Elimina | 204 / 404 |

**Campos obligatorios en POST y PUT:** `nombre`, `categoria`, `precio`, `stock`

---

## PARTE 3 — Cliente cURL `(20 minutos)` → archivo 05

Completa los TODOs en `05_curl_cliente.php`.

1. **GET** a la API REST → lista los productos en consola
2. **POST** a la API REST → crea un producto nuevo
3. **Llamada SOAP** via cURL → llama a `obtenerPrecio("Camiseta")` construyendo el envelope XML a mano

---

## BONUS — Frontend HTML `(si sobra tiempo)` → archivo 06

Completa los TODOs en `06_frontend.html`. Tiene formularios para:
- Listar productos (fetch GET)
- Crear producto (fetch POST)
- Eliminar producto (fetch DELETE)
- Consultar stock por SOAP (fetch con XML)

---

## Tabla de referencia rápida — Status codes REST

| Código | Cuándo usarlo |
|--------|--------------|
| 200 | GET o PUT exitoso |
| 201 | POST exitoso (recurso creado) |
| 204 | DELETE exitoso (sin body en respuesta) |
| 400 | Datos inválidos o campos obligatorios ausentes |
| 404 | Recurso no encontrado |
| 405 | Método HTTP no permitido |

## Tabla de referencia rápida — SOAP

| Elemento | Cuándo usarlo |
|----------|--------------|
| Respuesta normal | Petición válida, aunque el resultado sea negativo |
| `<soap:Fault>` | Solo cuando hay un error de protocolo o datos inválidos (falta parámetro, XML mal formado, método incorrecto) |

---

## Orden de resolución recomendado

```
02_soap_servidor.php   → empezar aquí
04_rest_servidor.php   → segunda parte
05_curl_cliente.php    → tercera parte
06_frontend.html       → bonus
```
