<?php
/*
=======================================
 ARRAYS: creación, push y eliminación
=======================================
*/

// Creación
$numeros = [1, 2, 3];

// Añadir elemento (push)
array_push($numeros, 4);
print_r($numeros);

// Eliminar elemento específico
unset($numeros[1]); // elimina el índice 1 (el valor 2)
print_r($numeros);
?>
