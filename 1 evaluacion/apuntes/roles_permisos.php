<?php
/*
============================================================
 SEGURIDAD: ROLES Y PERMISOS
============================================================
Conceptos:
- Rol → grupo de permisos (ej: "admin", "editor", "usuario").
- Permiso → acción específica (ej: "crear", "editar", "borrar").
- Se combinan para controlar el acceso a partes de la aplicación.

============================================================
 EJEMPLO COMPLETO DE CONTROL DE ROLES Y PERMISOS
============================================================
*/

session_start();

// --- Base de datos simulada ---
$usuarios = [
    "ricardo" => [
        "password" => "1234",
        "rol" => "admin"
    ],
    "ana" => [
        "password" => "abcd",
        "rol" => "editor"
    ],
    "luis" => [
        "password" => "0000",
        "rol" => "usuario"
    ]
];

// --- Permisos por rol ---
$permisos = [
    "admin" => ["crear", "editar", "borrar", "ver"],
    "editor" => ["editar", "ver"],
    "usuario" => ["ver"]
];

// --- Función para login ---
function login($user, $pass, $usuarios) {
    if (isset($usuarios[$user]) && $usuarios[$user]['password'] === $pass) {
        $_SESSION['usuario'] = $user;
        $_SESSION['rol'] = $usuarios[$user]['rol'];
        echo "Bienvenido $user (rol: " . $_SESSION['rol'] . ")<br>";
    } else {
        echo "Credenciales incorrectas.<br>";
    }
}

// --- Función para comprobar permisos ---
function tienePermiso($accion) {
    global $permisos;
    $rol = $_SESSION['rol'] ?? '';
    return in_array($accion, $permisos[$rol] ?? []);
}

// --- Simular login ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login($_POST['usuario'], $_POST['password'], $usuarios);
}

if (!isset($_SESSION['usuario'])) {
?>
<form method="POST">
    <h3>Iniciar sesión</h3>
    <input type="text" name="usuario" placeholder="Usuario" required><br>
    <input type="password" name="password" placeholder="Contraseña" required><br>
    <button type="submit">Entrar</button>
</form>
<?php
} else {
    echo "<hr><h3>Panel de Control</h3>";

    if (tienePermiso("ver")) {
        echo "✔ Puedes ver el contenido.<br>";
    }
    if (tienePermiso("editar")) {
        echo "✏️ Puedes editar contenido.<br>";
    }
    if (tienePermiso("crear")) {
        echo "🆕 Puedes crear nuevo contenido.<br>";
    }
    if (tienePermiso("borrar")) {
        echo "❌ Puedes eliminar contenido.<br>";
    }

    echo "<br><a href='?logout=1'>Cerrar sesión</a>";
}

// --- Cerrar sesión ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: roles_y_permisos.php");
}
?>
