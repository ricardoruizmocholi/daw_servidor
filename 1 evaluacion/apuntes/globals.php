<?php
/*
=======================================
 SUPERGLOBAL $GLOBALS
=======================================
- $GLOBALS es un array asociativo que contiene todas las variables globales del script.
- Se puede acceder a variables definidas fuera de una función desde dentro de ella.
- Clave = nombre de la variable global.

SINTAXIS:
$GLOBALS['nombre_variable']

Uso típico: acceder a variables globales dentro de funciones.
*/

$numero1 = 10;
$numero2 = 20;

function sumar() {
    // Acceso a las variables globales usando $GLOBALS
    $resultado = $GLOBALS['numero1'] + $GLOBALS['numero2'];
    echo "La suma es: $resultado\n";
}

sumar(); // Resultado: La suma es: 30

/*
Ejemplo práctico: contador global
*/
$contador = 0;

function incrementar() {
    $GLOBALS['contador']++;
}

incrementar();
incrementar();
echo "Contador: " . $contador; // Contador: 2
?>
