<?php
// abrimos sesion
session_start();

// comprobamos si no se ha enviado usuario rederigiremos a loging.
if(!isset($_SESSION['usuario'])){
    header('Location: login.php');
    exit;
}

echo "Bienvenido, ". $_SESSION['usuario']."<br>";
echo "Tu rol es: ".$_SESSION['rol']."<br>";
echo "<a href='cerrar.php'> Cerrar Sesion </a>";

?>