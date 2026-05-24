<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


<?php
// Recirbir 4 numeros enteros mediante parametros de URL

$n1 = $_GET['n1'];
$n2 = $_GET['n2'];
$n3 = $_GET['n3'];
$n4 = $_GET['n4'];

// crear una array vacia

$lista = [];

//Agregamos los numeros a la lista vacia

array_push($lista, $n1, $n2, $n3, $n4);
echo "Numeros introducidos en el array";
echo $lista;
echo "Prueba";
print_r($lista);
echo"<br><br>";

// Eliminar el ultimo elemento y guardarlo

$ultimo = array_pop($lista);
echo"Lista despues de eliminar el ultimo numero";
print_r($lista);
echo"<br><br>";

//Eliminar el 2 numero y mostar

array_splice($lista,1,1);
echo"Lista despues de eliminar el 2º numero";
print_r($lista);
echo"<br><br>";

//Aritmetica

$suma = array_sum($lista) * $ultimo;
$pruducto = array_product($lista) * $ultimo;

echo "Suma multiplicada por el numero eliminado: $suma<br><br>"  ;
echo "Producto multiplicada por el numero eliminado: $pruducto"  ;

?>
    
</body>
</html>