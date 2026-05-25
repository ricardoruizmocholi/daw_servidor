# EJ04 — Completa el XML SOAP

Tiempo estimado: 10 minutos.

El SOAP siempre tiene la misma estructura. Si la memorizas, el resto es solo rellenar.

---

## Parte A — Estructura básica: rellena los huecos `___`

```xml
<?xml version="1.0" encoding="___"?>
<soap:___ xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">

  <soap:___>
    <sumar>
      <a>___</a>
      <b>___</b>
    </sumar>
  </soap:___>

</soap:___>
```

Preguntas sobre este XML:
1. ¿Qué tag es obligatorio y cuál es opcional?
2. ¿Cómo sabe el servidor que la operación se llama "sumar"?
3. ¿Qué codificación de texto se debe usar siempre?

_Tus respuestas:_
```
1.
2.
3.
```

---

## Parte B — Añade un Header de sesión

Reescribe el XML de la Parte A añadiendo un Header con un token de sesión:
- El Header debe contener un elemento `<sesion>` con un hijo `<token>ABC-123</token>`
- El Body sigue siendo el mismo (`<sumar>` con `a=8` y `b=3`)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">

  <!-- Escribe aquí el Header completo -->


  <!-- El Body ya está completo — no lo toques -->
  <soap:Body>
    <sumar>
      <a>8</a>
      <b>3</b>
    </sumar>
  </soap:Body>

</soap:Envelope>
```

---

## Parte C — Respuesta del servidor: rellena los huecos

El servidor recibió la petición de sumar 8+3 y devuelve el resultado.
Completa la respuesta:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:___ xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:___>
    <___Response>
      <resultado>___</resultado>
    </___Response>
  </soap:___>
</soap:___>
```

---

## Parte D — SOAP Fault: rellena los huecos

El servidor recibió una petición mal formada y devuelve un error.
Completa el Fault:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:___>
      <___code>SOAP-ENV:Client</___code>
      <___string>No se puede dividir entre 0.</___string>
    </soap:___>
  </soap:Body>
</soap:Envelope>
```

Preguntas sobre el Fault:
1. ¿Cuándo debes usar Fault en vez de una respuesta normal?
2. ¿Qué significa `SOAP-ENV:Client` en el faultcode?

_Tus respuestas:_
```
1.
2.
```

---

---
---

# RESPUESTAS

## Parte A

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">

  <soap:Body>
    <sumar>
      <a>8</a>
      <b>3</b>
    </sumar>
  </soap:Body>

</soap:Envelope>
```

Respuestas:
1. **Body es obligatorio**; Header es opcional.
2. El servidor lee el nombre del elemento hijo del Body: `<sumar>` → operación = "sumar".
3. Siempre **UTF-8**.

## Parte B — Con Header

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">

  <soap:Header>
    <sesion>
      <token>ABC-123</token>
    </sesion>
  </soap:Header>

  <soap:Body>
    <sumar>
      <a>8</a>
      <b>3</b>
    </sumar>
  </soap:Body>

</soap:Envelope>
```

## Parte C — Respuesta

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <sumarResponse>
      <resultado>11</resultado>
    </sumarResponse>
  </soap:Body>
</soap:Envelope>
```

Patrón: el nombre de la respuesta = nombre de la operación + "Response".

## Parte D — Fault

```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>SOAP-ENV:Client</faultcode>
      <faultstring>No se puede dividir entre 0.</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
```

Respuestas:
1. Usa Fault cuando hay un **error de protocolo o validación**: datos mal formados, parámetros que faltan, método no POST, o incumplimiento de reglas de protocolo. NO lo uses para resultados negativos de negocio (esos van como respuesta normal con `permitido=false`).
2. `Client` significa que el error es culpa del cliente (petición incorrecta). `Server` sería un error interno del servidor.
