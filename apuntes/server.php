<?php
/*
=======================================
 SUPERGLOBAL $_SERVER
=======================================
- Contiene información sobre el entorno del servidor y la ejecución del script.
- Se usa para obtener datos como la URL, nombre del host, tipo de petición, etc.

EJEMPLOS COMUNES:
*/

echo "Nombre del script actual: " . $_SERVER['PHP_SELF'] . "<br>";
echo "Nombre del servidor: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Método de petición: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "User Agent del navegador: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
echo "Dirección IP del cliente: " . $_SERVER['REMOTE_ADDR'] . "<br>";

/*
Uso práctico: redirección condicional según método de petición
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Se ha enviado un formulario";
} else {
    echo "Mostrar formulario";
}
?>
