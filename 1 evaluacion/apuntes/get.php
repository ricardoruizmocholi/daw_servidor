<?php
/*
=======================================
 SUPERGLOBAL $_GET
=======================================
- Recoge datos enviados a través de la URL mediante el método GET.
- Los datos se envían como parámetros en la URL (?nombre=valor).

SINTAXIS:
$_GET['nombre']

EJEMPLO:
URL → http://localhost/get.php?nombre=Ricardo&edad=25
*/

if (isset($_GET['nombre']) && isset($_GET['edad'])) {
    echo "Nombre: " . $_GET['nombre'] . "<br>";
    echo "Edad: " . $_GET['edad'] . "<br>";
} else {
    echo "Pasa parámetros en la URL, ejemplo: ?nombre=Ricardo&edad=25";
}
?>
