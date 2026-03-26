<?php
/*
=======================================
 FUNCIONES DE MANIPULACIÓN DE STRINGS
=======================================
*/

// explode() → divide un string en un array
$frase = "PHP,HTML,CSS,JS";
$lenguajes = explode(",", $frase);
print_r($lenguajes);

// trim(), ltrim(), rtrim() → eliminan espacios
$texto = "  Hola mundo  ";
echo trim($texto) . "<br>";

// strstr() → busca una subcadena
echo strstr("ricardo@gmail.com", "@") . "<br>"; // @gmail.com

// strtolower(), strtoupper() → cambia el caso
echo strtoupper("hola") . "<br>";
echo strtolower("HOLA") . "<br>";

// strcmp() / strcasecmp() → comparan strings
echo strcmp("Hola", "hola"); // sensible a mayúsculas
echo strcasecmp("Hola", "hola"); // no sensible

// strlen() → longitud
echo strlen("PHP"); // 3
?>
