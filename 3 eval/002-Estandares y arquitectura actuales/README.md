# 002 — Estándares y arquitectura actuales

## ¿Qué se practica aquí?
Aquí descubres **SOAP**, el protocolo "formal" de servicios web. A diferencia de REST, SOAP envuelve cada petición en un **sobre XML estricto** (Envelope → Body). Es como mandar una carta certificada frente a un WhatsApp: más burocrática, pero con un **contrato firmado (el WSDL)** que garantiza exactamente qué puedes pedir y qué recibirás. El WSDL es la carta del restaurante oficial, con tipos de datos, formatos de petición y dirección del servicio.

## Archivos

| Archivo | Propósito |
|---|---|
| `calculadora-soap.php` | Servidor SOAP manual: parsea el XML de entrada con `DOMDocument`, ejecuta la operación y responde con XML |
| `calculadora.wsdl` | Contrato del servicio: define tipos (`xsd`), mensajes, operaciones y dirección del servidor |
| `calculadora-soap.html` | Cliente JavaScript que construye el XML SOAP a mano y lo envía con `fetch()` |
| `ampliacionCalculadora.txt` | Enunciado: ampliar la calculadora con multiplicación y división (con resto y error por división entre 0) |

## Conceptos clave
- **SOAP Envelope**: la estructura obligatoria de todo mensaje SOAP — `Envelope > Header (opt.) > Body`
- **WSDL**: `types` (esquema XSD), `message`, `portType` (operaciones), `binding` (protocolo), `service` (URL)
- **`DOMDocument` y `DOMXPath`**: clases PHP para parsear y navegar documentos XML
- **SOAP Fault**: la única forma correcta de reportar errores en SOAP — tiene `faultcode` y `faultstring`
- **Construcción de XML en JS**: template literals para generar el XML SOAP del cliente

## Estructura de un mensaje SOAP

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>              <!-- Aquí va la operación y sus datos -->
    <sumar>
      <a>8</a>
      <b>3</b>
    </sumar>
  </soap:Body>
</soap:Envelope>
```

```xml
<!-- Respuesta del servidor -->
<soap:Envelope ...>
  <soap:Body>
    <sumarResponse>
      <resultado>11</resultado>
    </sumarResponse>
  </soap:Body>
</soap:Envelope>
```

## Diagrama del flujo SOAP

```
CLIENTE (calculadora-soap.html)          SERVIDOR (calculadora-soap.php)
         │                                         │
         │  POST calculadora-soap.php              │
         │  Content-Type: text/xml          ─────►│
         │  Body: XML Envelope con <sumar>         │ DOMDocument::loadXML()
         │                                         │ DOMXPath para navegar
         │                                         │ switch($operacion) → sumar
         │  ◄── 200 OK                        ─── │
         │  Body: XML Envelope con <sumarResponse> │
```

## Para el examen
⚡ El **WSDL es el "contrato"** del servicio — define exactamente qué operaciones existen, con qué parámetros y qué devuelven. Sin WSDL no hay SOAP estándar.

⚡ Un **SOAP Fault** siempre tiene `<faultcode>` y `<faultstring>` — es la única manera correcta de reportar errores en SOAP. Nunca devuelvas un JSON de error en un servicio SOAP.

⚡ SOAP usa **siempre POST**, nunca GET — el "verbo" de la operación (sumar, restar...) va dentro del XML Body, no en la URL.
