# 001 — Tecnologías y protocolos implicados

## ¿Qué se practica aquí?
Aquí construyes una **tienda online real** con varios servicios PHP independientes, cada uno con una responsabilidad concreta. Es como un restaurante donde el camarero (JavaScript en el navegador) llama a diferentes estaciones de cocina (servicios PHP): una para ver la carta de productos, otra para consultar el stock, otra para registrar el pedido. Cada servicio devuelve **JSON**, el formato estándar de intercambio en REST.

## Archivos

| Archivo | Propósito |
|---|---|
| `conexion_mysql.php` | Función `obtenerPDO()`: crea y devuelve una conexión PDO a la BD `tienda_servicios` |
| `servicio_productos.php` | Endpoint **GET** — devuelve todos los productos de la tabla `producto` en JSON |
| `servicio_stock.php` | Endpoint **POST** — recibe un ID de producto y devuelve el stock disponible |
| `servicio_pedidos.php` | Endpoint **POST** — crea un pedido con transacción SQL, valida stock y lo descuenta |
| `tienda.html` | Cliente completo: selector de producto, consulta de stock y formulario de pedido |
| `tienda_servicios.sql` | Esquema SQL con tablas `producto`, `stock` y `pedido`, con datos de prueba |
| `serviciosVideojuegos.txt` | Enunciado de ampliación: servicio de videojuegos con filtros, disponibilidad y precio |

## Conceptos clave
- **REST informal**: servicios PHP que devuelven JSON sin framework, perfectos para entender el mecanismo base
- **PDO**: conexión a MySQL con `prepare()`, `bindParam()`, `execute()` y `fetchAll()`
- **Transacciones SQL**: `beginTransaction()`, `commit()`, `rollBack()` para garantizar consistencia
- **`fetch()` en JavaScript**: llamadas asíncronas al servidor sin recargar la página
- **`header("Content-Type: application/json")`**: le dice al cliente que la respuesta es JSON
- **Separación de responsabilidades**: un archivo PHP por función de negocio

## Diagrama del flujo de un pedido

```
NAVEGADOR (tienda.html)                  SERVIDOR PHP
         │                                    │
         │  POST servicio_stock.php            │
         │  Body: {"id_producto": 3}    ────► │ SELECT stock WHERE id=3
         │  ◄──── 200 OK + JSON (stock)  ─── │
         │                                    │
         │  POST servicio_pedidos.php          │
         │  Body: {"id_producto":3,"cant":2}──►│ BEGIN TRANSACTION
         │                                    │   INSERT INTO pedido
         │                                    │   UPDATE stock SET cantidad-=2
         │                                    │ COMMIT
         │  ◄──── 200 OK + JSON (pedido)  ── │
```

## Para el examen
⚡ `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` hace que los errores de BD lancen excepciones PHP — sin esto, los errores SQL pasan en silencio y no te enteras de que algo falla.

⚡ Las **transacciones** garantizan que "validar stock + crear pedido + descontar stock" ocurre **todo o nada** — si algo falla a mitad, `rollBack()` deshace todo y la BD queda limpia.

⚡ `json_encode($datos)` + `header("Content-Type: application/json")` es la receta mínima para crear un endpoint REST en PHP puro, sin frameworks.
