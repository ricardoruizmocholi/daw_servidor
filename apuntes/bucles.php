<?php
/*
=======================================
 BUCLES: foreach / for-in / foreach con objetos
=======================================
*/

// FOREACH con arrays
$colores = ["rojo", "verde", "azul"];
foreach ($colores as $color) {
    echo "Color: $color<br>";
}

// FOREACH con clave => valor
$persona = ["nombre" => "Ricardo", "edad" => 25];
foreach ($persona as $clave => $valor) {
    echo "$clave: $valor<br>";
}

// FOREACH con objetos
class Usuario {
    public $nombre = "Ana";
    public $edad = 30;
}
$usuario = new Usuario();
foreach ($usuario as $prop => $valor) {
    echo "$prop: $valor<br>";
}
?>
