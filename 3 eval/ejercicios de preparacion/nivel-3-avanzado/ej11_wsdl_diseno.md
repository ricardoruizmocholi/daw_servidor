# EJ11 — Diseña el WSDL de un servicio de reservas de hotel

## Enunciado

Crea el fichero WSDL completo para el servicio `ServicioReservas`.

El servicio tiene **una sola operación**: `consultarDisponibilidad`

**Parámetros de entrada:**
- `fecha_entrada` (string, formato YYYY-MM-DD)
- `fecha_salida` (string, formato YYYY-MM-DD)
- `num_personas` (int)

**Respuesta:**
- `disponible` (boolean)
- `precio_noche` (double)
- `mensaje` (string)

---

## Tu WSDL (complétalo aquí):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<definitions
    name="ServicioReservas"
    targetNamespace="urn:ServicioReservas"
    xmlns:tns="urn:ServicioReservas"
    xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://schemas.xmlsoap.org/wsdl/">

    <!-- 1. TYPES -->
    <types>
        <xsd:schema targetNamespace="urn:ServicioReservas">

            <!-- TODO: define el elemento consultarDisponibilidad con los 3 campos de entrada -->

            <!-- TODO: define el elemento consultarDisponibilidadResponse con los 3 campos de salida -->

        </xsd:schema>
    </types>

    <!-- 2. MESSAGES -->
    <!-- TODO: define consultarDisponibilidadRequest y consultarDisponibilidadResponse -->

    <!-- 3. PORT TYPE -->
    <!-- TODO: define la operación consultarDisponibilidad con input y output -->

    <!-- 4. BINDING -->
    <!-- TODO: binding SOAP/HTTP con la operación -->

    <!-- 5. SERVICE -->
    <!-- TODO: service con la dirección http://localhost/reservas/servicio.php -->

</definitions>
```

---

## Preguntas de verificación

1. ¿Qué tipo XSD usarías para representar `true`/`false`?
2. ¿Qué tipo XSD usarías para un precio como `89.50`?
3. ¿Qué nombre le darías al elemento de respuesta por convención?
