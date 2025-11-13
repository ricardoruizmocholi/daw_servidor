<?php
/*
=======================================
 SUPERGLOBAL $_POST
=======================================
- Recoge datos enviados desde un formulario HTML con el método POST.
- Es más seguro que GET porque no expone los datos en la URL.

SINTAXIS:
$_POST['nombre_campo']
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    echo "Hola, $nombre!";
} else {
?>
    <form method="POST">
        <label>Nombre: <input type="text" name="nombre"></label>
        <button type="submit">Enviar</button>
    </form>
<?php
}
?>
