# 3ª Evaluación — Desarrollo en Entorno Servidor

## ¿Qué se practica aquí?
Esta evaluación cubre el núcleo de la programación de servicios web: cómo dos máquinas se hablan a través de la red usando protocolos estándar. Como en un restaurante, hay un **cliente** (el que pide) y un **servidor** (el que cocina y sirve). Aquí aprenderás a construir y consumir ambos lados de esa conversación, usando los dos grandes protocolos del sector: **REST** (moderno, informal, JSON) y **SOAP** (formal, contractual, XML).

## Carpetas

| Carpeta | Contenido |
|---|---|
| `001-tecnologias y protocolos implicados` | API REST informal con PHP y MySQL — la tienda online |
| `002-Estandares y arquitectura actuales` | Protocolo SOAP y definición WSDL — la calculadora |
| `003-Generacion de un servicio` | Diseño "contract-first": WSDL primero, implementación después |
| `004-interfaces de un servicio` | SOAP con cabeceras (Headers) para metadatos de sesión |
| `005-Consumo de un servicio web` | Consumo de APIs REST completas + especificación OpenAPI |
| `007-Generacion dinamica de paginas interactivas` | HTML dinámico con Fetch y PHP sin recargar la página |
| `008-Obtecion remota de informacion` | El servidor como intermediario: cURL para llamar a APIs externas |
| `009-Modificacion de la estuctura y contenido` | Fragmentos HTML generados en servidor e inyectados en el DOM |

## Conceptos clave de la evaluación
- **HTTP**: métodos (GET, POST, PUT, PATCH, DELETE), status codes (2xx, 4xx, 5xx), headers, body
- **REST**: diseño de recursos, URLs semánticas, JSON como formato de intercambio
- **SOAP**: envelopes XML, WSDL como contrato, operaciones tipadas, SOAP Fault
- **PHP como servidor**: servicios REST manuales, parseo de XML, PDO para bases de datos
- **JavaScript como cliente**: Fetch API, async/await, manipulación del DOM
- **Bases de datos**: PDO, consultas preparadas, transacciones SQL

## Diagrama general: cliente-servidor

```
CLIENTE (Navegador / HTML + JS)          SERVIDOR (PHP)
         │                                     │
         │  HTTP Request (REST o SOAP)   ────► │
         │  Método + URL + Headers + Body       │  lógica de negocio
         │                                     │  acceso a BD o fichero
         │  ◄────  HTTP Response           ─── │
         │  Status code + Headers + Body        │
         │  (JSON o XML según el protocolo)     │
```

## Para el examen
⚡ **REST** usa JSON y URLs legibles (`/productos/5`); **SOAP** usa XML con un "sobre" estricto. Son dos filosofías distintas — REST es flexible, SOAP es contractual.

⚡ En REST, el **método HTTP** dice QUÉ haces (GET=leer, POST=crear, DELETE=borrar) y la **URL** dice SOBRE QUÉ lo haces. En SOAP, el verbo va dentro del XML.

⚡ SOAP requiere siempre un `Body`; los `Header` son opcionales y transportan metadatos (sesión, autenticación, trazabilidad) — nunca lógica de negocio.
