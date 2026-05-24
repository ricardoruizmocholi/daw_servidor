# 004 — Interfaces de un servicio

## ¿Qué se practica aquí?
Aquí profundizas en las **cabeceras SOAP (Headers)**, que son el equivalente a los datos del sobre de una carta: no son el contenido principal, pero llevan información clave como quién envía, qué ID tiene la petición, o si tiene permisos. También practicas diseñar un **servicio completo desde cero**: WSDL + PHP servidor + HTML cliente, siguiendo el proceso profesional real.

## Archivos

| Archivo | Propósito |
|---|---|
| `envioPostal.php` | Servicio SOAP de envíos: lee un `requestId` del Header y calcula precio según peso, zona y urgencia |
| `envioPostal.wsdl` | Contrato WSDL del servicio postal |
| `envioPostal.html` | Cliente SOAP que envía el `requestId` en el Header y muestra la respuesta completa |
| `accesoAExamen.txt` | Enunciado: crear servicio de validación de acceso a examen (nombre, edad, matriculado) |
| `accesoAExamen.wsdl` | Contrato WSDL del servicio de acceso a examen |
| `accesoAExamen.php` | Servicio SOAP que valida si un alumno puede acceder al examen (>16 años y matriculado) |
| `accesoAExamen.html` | Cliente SOAP del servicio de acceso a examen |

## Conceptos clave
- **SOAP Headers**: parte del Envelope para metadatos — `requestId`, tokens de sesión, timestamp
- **Header en respuesta**: el servidor puede devolver información en el Header de la respuesta (ej. confirmar el `requestId` recibido)
- **Diferencia Header vs Body**: Header = metadatos de la llamada; Body = la operación y sus datos
- **Validación robusta en SOAP**: campos obligatorios, tipos correctos, rangos lógicos
- **SOAP Fault para validación**: si los datos son inválidos, se responde con Fault, no con un resultado negativo

## Estructura del envío postal (Header + Body)

```xml
<!-- Petición del cliente -->
<soap:Envelope>
  <soap:Header>
    <peticionInfo>
      <requestId>REQ-20250524-001</requestId>   <!-- metadato de trazabilidad -->
    </peticionInfo>
  </soap:Header>
  <soap:Body>
    <calcularEnvio>
      <peso>2.5</peso>
      <zona>peninsula</zona>
      <urgente>true</urgente>
    </calcularEnvio>
  </soap:Body>
</soap:Envelope>

<!-- Respuesta del servidor — también lleva Header -->
<soap:Envelope>
  <soap:Header>
    <respuestaInfo>
      <servidor>ServicioEnvioPostalPHP</servidor>
      <requestId>REQ-20250524-001</requestId>   <!-- el servidor confirma el ID recibido -->
    </respuestaInfo>
  </soap:Header>
  <soap:Body>
    <calcularEnvioResponse>
      <precio>12.50</precio>
      <plazoDias>1</plazoDias>
      <zona>peninsula</zona>
      <urgente>true</urgente>
    </calcularEnvioResponse>
  </soap:Body>
</soap:Envelope>
```

## Lógica de validación del acceso a examen

```
nombre vacío           → SOAP Fault (error de entrada, no resultado negativo)
edad no numérica       → SOAP Fault
edad > 16 Y matriculado = true  → permitido: true  + mensaje explicativo
edad <= 16             → permitido: false + "Debes tener más de 16 años"
no matriculado         → permitido: false + "No estás matriculado"
```

## Para el examen
⚡ El Header SOAP es **opcional** — el Body es **obligatorio**. Si no recibes Header no es un error de protocolo; simplemente no hay metadatos adicionales.

⚡ La **validación siempre ocurre en el servidor**, aunque el cliente ya valide. El cliente puede ser modificado o saltarse la validación — el servidor es la última línea de defensa.

⚡ Un servicio bien diseñado distingue entre **error de protocolo** (datos mal formados → `Fault`) y **resultado negativo** (alumno no permitido → respuesta normal con `permitido: false`).
