# EJ16 — Simulacro de Examen Trimestral

**Tiempo: 90 minutos · Sin mirar los apuntes ni los ejemplos**

---

## PARTE 1 — Teoría rápida (15 min · ~20% de la nota)

Responde en 2-3 líneas como máximo:

1. ¿Qué diferencia hay entre SOAP Fault y una respuesta SOAP con resultado negativo?

2. ¿Qué hace `CURLOPT_RETURNTRANSFER` y qué ocurre si no lo pones?

3. Un cliente recibe `204 No Content`. ¿Qué método HTTP acaba de ejecutar? ¿Por qué no hay body?

4. ¿Qué es `PATH_INFO` y cómo lo lees en PHP?

5. ¿Por qué `file_get_contents("php://input")` en vez de `$_POST`?

---

## PARTE 2 — Servicio REST (30 min · ~35% de la nota)

Implementa el fichero `reservas.php`.

**Recurso:** reservas de hotel  
**Campos:** `id`, `cliente`, `fecha_entrada`, `fecha_salida`, `num_personas`, `confirmada` (boolean)  
**Almacenamiento:** fichero `reservas.json`

Implementa **todas** las operaciones:

| Ruta | Método | Descripción | Status |
|---|---|---|---|
| `/reservas` | GET | Devuelve todas las reservas | 200 |
| `/reservas/{id}` | GET | Devuelve una reserva | 200 / 404 |
| `/reservas` | POST | Crea reserva (requiere cliente, fecha_entrada, fecha_salida, num_personas) | 201 / 400 |
| `/reservas/{id}` | PATCH | Actualiza campos (solo los que vienen) | 200 / 404 |
| `/reservas/{id}` | DELETE | Elimina la reserva | 204 / 404 |

Requisitos extra:
- Si `num_personas` <= 0 → 400 Bad Request
- `confirmada` tiene valor por defecto `false` al crear
- `id` se genera automáticamente

---

## PARTE 3 — Servicio SOAP (35 min · ~35% de la nota)

Implementa el fichero `descuento.php` y su WSDL `descuento.wsdl`.

**Servicio:** Calculadora de descuentos  
**Operación:** `calcularDescuento(precio, porcentaje)` → `precio_final` (double), `ahorro` (double)

Requisitos:
- Si `precio` no es numérico → SOAP Fault
- Si `porcentaje` < 0 o > 100 → SOAP Fault ("El porcentaje debe estar entre 0 y 100")
- Si `precio` <= 0 → SOAP Fault
- Fórmula: `ahorro = precio × (porcentaje / 100)` · `precio_final = precio − ahorro`
- Redondea ambos a 2 decimales

El **WSDL** debe ser completo (types, message, portType, binding, service).

---

## PARTE 4 — Cliente HTML (10 min · ~10% de la nota)

Implementa `cliente_descuento.html`:
- Formulario con campos `precio` y `porcentaje`
- Al enviar, construye el SOAP y llama a `descuento.php` con `fetch()`
- Muestra el resultado o el Fault en pantalla

---

## Criterios de corrección

| Criterio | Puntos |
|---|---|
| Teoría correcta (5 preguntas) | 2 |
| REST: GET all + GET one correctos | 1 |
| REST: POST con validación correcta | 1 |
| REST: PATCH solo los campos recibidos | 0.5 |
| REST: DELETE con 204 sin body | 0.5 |
| SOAP: WSDL completo y correcto | 1 |
| SOAP: Validaciones con Fault correcto | 1.5 |
| SOAP: Fórmula y respuesta correcta | 1 |
| Cliente HTML funcional | 1.5 |
| **TOTAL** | **10** |

---

## Cuando termines

1. Prueba el REST con el navegador o Postman
2. Prueba el SOAP con el cliente HTML
3. Compara tu WSDL con los de la carpeta `002` y `004` de los apuntes
4. Revisa que todos los status codes sean los correctos (no pongas 200 en un POST exitoso)
