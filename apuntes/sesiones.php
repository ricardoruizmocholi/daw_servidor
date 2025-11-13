<?php
/*
============================================================
 SESIONES EN PHP
============================================================
Definición:
- Las sesiones almacenan datos del usuario en el servidor.
- Cada usuario tiene un ID de sesión único (session_id()).
- A diferencia de las cookies, los datos no se guardan en el navegador.

SINTAXIS BÁSICA:
session_start();
$_SESSION['clave'] = valor;
unset($_SESSION['clave']);
session_destroy();

============================================================
 INICIAR SESIÓN Y GUARDAR DATOS
============================================================
*/

session_start(); // siempre al principio del script

$_SESSION['usuario'] = "Ricardo";
$_SESSION['rol'] = "admin";
$_SESSION['ultimo_acceso'] = date("H:i:s");

echo "Sesión iniciada para: " . $_SESSION['usuario'] . "<br>";
echo "Rol: " . $_SESSION['rol'] . "<br>";
echo "Hora: " . $_SESSION['ultimo_acceso'] . "<br>";

/*
============================================================
 COMPROBAR SI EL USUARIO ESTÁ LOGUEADO
============================================================
*/
if (isset($_SESSION['usuario'])) {
    echo "Usuario autenticado correctamente.<br>";
} else {
    echo "No hay sesión activa.<br>";
}

/*
============================================================
 CERRAR SESIÓN
============================================================
*/
// Descomentar para probar el cierre completo
/*
session_unset();  // elimina todas las variables
session_destroy(); // destruye la sesión
echo "Sesión cerrada.";
*/

/*
============================================================
 SESIONES CON TIEMPO DE EXPIRACIÓN
============================================================
*/

$tiempo_inactividad = 600; // 10 minutos
if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad'] > $tiempo_inactividad)) {
    session_unset();
    session_destroy();
    echo "Sesión expirada por inactividad.<br>";
}
$_SESSION['ultima_actividad'] = time();
?>
