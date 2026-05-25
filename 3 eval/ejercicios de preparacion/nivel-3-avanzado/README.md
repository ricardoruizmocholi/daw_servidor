# Nivel 3 — Avanzado (50–75%)

## Prerequisito

Dominar los niveles 1 y 2, especialmente:
- Parsear XML con DOMDocument y DOMXPath
- Leer PATH_INFO para la URL del recurso
- Leer el body JSON de POST/PATCH con `php://input`

## Ejercicios

| Archivo | Tipo | Tiempo estimado |
|---|---|---|
| `ej09_rest_crud.php` | PHP — API REST completa GET/POST/PATCH/DELETE | 25 min |
| `ej10_soap_validacion.php` | PHP — SOAP con validación + Fault vs respuesta negativa | 20 min |
| `ej11_wsdl_diseno.md` | Diseño — crear un WSDL completo desde un enunciado | 20 min |
| `ej12_cliente_soap.html` | HTML+JS — cliente SOAP completo con parseo de respuesta | 20 min |

## Lo que debes saber al acabar este nivel

- Implementar los 5 métodos REST (GET all, GET one, POST, PATCH, DELETE)
- Distinguir SOAP Fault (error de protocolo) de respuesta negativa de negocio
- Diseñar un WSDL con `types`, `message`, `portType`, `binding`, `service`
- Construir el XML SOAP en JavaScript y parsear la respuesta con DOMParser
