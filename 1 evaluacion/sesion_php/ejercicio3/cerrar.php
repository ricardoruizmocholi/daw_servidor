<?php
session_start();
session_unset();
session_destroy();

// Elimina la cookie
setcookie('usuario', '', time() - 3600);

header('Location: login.php');
?>
