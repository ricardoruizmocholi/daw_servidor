# 003 — Generación de un servicio

## ¿Qué se practica aquí?
Aquí practicas el enfoque **"contract-first"**: primero diseñas el WSDL (el contrato), y después implementas el servicio PHP que lo cumple. Es como diseñar la carta del restaurante antes de contratar a los cocineros — primero decides qué se puede pedir y cómo se sirve, luego lo implementas. También introduces las **cabeceras de sesión SOAP**: un token en el Header que identifica quién está haciendo la petición.

## Archivos

| Archivo | Propósito |
|---|---|
| `prestamo.wsdl` | Contrato del servicio: define la operación `consultarPrestamo` con sus tipos de entrada y salida |
| `prestamo.txt` | Enunciado: implementar el servicio y el cliente a partir del WSDL, con cabeceras de sesión |
| `prestamo.php` | Servidor SOAP que implementa `consultarPrestamo`, validando el token de sesión del Header |
| `prestamo.html` | Cliente que envía DNI + código de libro con un token de sesión en el SOAP Header |

## Conceptos clave
- **Contract-first**: el WSDL define la interfaz; PHP la implementa siguiendo ese contrato
- **SOAP Headers de sesión**: el Header transporta un token de sesión (metadato), no datos de negocio
- **`consultarPrestamo`**: recibe `dni` (string) y `codigoLibro` (string), devuelve `puede_prestar` (boolean), `mensaje` (string) y `dias_maximos` (int)
- **Validación en servidor**: siempre verificar que el token de sesión existe antes de procesar la operación
- **Booleanos en XML**: se representan como `true`/`false` en minúsculas, no como 1/0

## Estructura del mensaje con Header de sesión

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <sesion>                          <!-- Metadato: quién hace la petición -->
      <token>ABC-123-XYZ</token>
    </sesion>
  </soap:Header>
  <soap:Body>                         <!-- Lógica de negocio: qué se pide -->
    <consultarPrestamo>
      <dni>12345678A</dni>
      <codigoLibro>LIB-042</codigoLibro>
    </consultarPrestamo>
  </soap:Body>
</soap:Envelope>
```

## Diagrama del flujo con sesión

```
CLIENTE (prestamo.html)                  SERVIDOR (prestamo.php)
         │                                         │
         │  POST prestamo.php                      │
         │  Header SOAP: <token>ABC</token> ─────►│ 1. Lee el Header → valida token
         │  Body: <consultarPrestamo>              │    Si token vacío → SOAP Fault
         │    <dni>12345678A</dni>                 │ 2. Lee Body → dni + codigoLibro
         │    <codigoLibro>LIB-42</codigoLibro>   │ 3. Comprueba préstamo
         │  </consultarPrestamo>                   │ 4. Construye respuesta XML
         │                                         │
         │  ◄── <consultarPrestamoResponse>   ─── │
         │    <puede_prestar>true</puede_prestar>  │
         │    <mensaje>Préstamo permitido</mensaje>│
         │    <dias_maximos>14</dias_maximos>      │
```

## Para el examen
⚡ El flujo **contract-first** es: diseñar WSDL → implementar servidor → implementar cliente. El WSDL es la "interfaz" que ambos lados respetan — cambiar el WSDL obliga a cambiar tanto el servidor como el cliente.

⚡ El **SOAP Header** está diseñado para **metadatos de la llamada** (quién llama, con qué token, timestamp) — la lógica de negocio siempre va en el Body.

⚡ `puede_prestar` es un booleano XML — en XML se representa como `true`/`false` (en minúsculas), no como 1/0 ni como "SI"/"NO".
