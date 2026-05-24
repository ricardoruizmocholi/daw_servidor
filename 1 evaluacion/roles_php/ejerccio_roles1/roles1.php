<?php
session_start();

// ----------------------------------------------------
// BASE DE DATOS SIMULADA (usuarios Pokémon)
// ----------------------------------------------------
$usuarios = [
    ["usuario" => "pikachu", "password" => "trueno", "rol" => "entrenador"],
    ["usuario" => "charizard", "password" => "fuego", "rol" => "lider_gimnasio"],
    ["usuario" => "mewtwo", "password" => "psiquico", "rol" => "legendario"],
    ["usuario" => "bulbasaur", "password" => "hoja", "rol" => "novato"],
    ["usuario" => "snorlax", "password" => "zzz", "rol" => "guardian"]
];

// ----------------------------------------------------
// FUNCIONES PARA COOKIES
// ----------------------------------------------------
function crearCookieUsuario($usuario, $rol, $password) {
    $datos = json_encode(["usuario" => $usuario, "password" => $password, "rol" => $rol]);
    setcookie("auth", $datos, time() + 3600, "/"); // Dura 1 hora
}

function eliminarCookieUsuario() {
    setcookie("auth", "", time() - 3600, "/");
}

// ----------------------------------------------------
// LOGIN
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["usuario"], $_POST["contraseña"])) {
    $usuario = trim($_POST["usuario"]);
    $clave = trim($_POST["contraseña"]);

    foreach ($usuarios as $u) {
        if ($u["usuario"] === $usuario && $u["password"] === $clave) {
            crearCookieUsuario($u["usuario"], $u["rol"], $u["password"]);
            header("Location: roles1.php");
            exit;
        }
    }
    echo "<h3 style='color:red;'>⚠️ Usuario o contraseña incorrectos</h3>";
}

// ----------------------------------------------------
// MIGRAR COOKIE A SESIÓN
// ----------------------------------------------------
if (!isset($_SESSION["usuario"]) && isset($_COOKIE["auth"])) {
    $datos = json_decode($_COOKIE["auth"], true);
    if ($datos && isset($datos["usuario"], $datos["password"], $datos["rol"])) {
        $_SESSION["usuario"] = $datos["usuario"];
        $_SESSION["rol"] = $datos["rol"];
        $_SESSION["password"] = $datos["password"];
        eliminarCookieUsuario();
    }
}

// ----------------------------------------------------
// LOGOUT
// ----------------------------------------------------
if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    eliminarCookieUsuario();
    header("Location: roles1.php");
    exit;
}

// ----------------------------------------------------
// DATOS DE SESIÓN
// ----------------------------------------------------
$usuario = $_SESSION["usuario"] ?? null;
$rol = $_SESSION["rol"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Pokémon con Cookies y Sesiones</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        form { margin-bottom: 20px; }
        .panel { border: 1px solid #ccc; padding: 10px; border-radius: 8px; margin-top: 10px; }
        .legendario { color: purple; }
        .lider_gimnasio { color: red; }
        .entrenador { color: orange; }
        .guardian { color: green; }
        .novato { color: gray; }
    </style>
</head>
<body>

<h1>⚙️ Roles Pokémon con Sesiones y Cookies</h1>

<?php if (!$usuario): ?>
    <form method="post" action="">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>
        <label>Contraseña:</label><br>
        <input type="password" name="contraseña" required><br><br>
        <button type="submit">Entrar</button>
    </form>

<?php else: ?>

    <p>👋 Bienvenido <strong><?= htmlspecialchars($usuario) ?></strong>
    (rol: <span class="<?= htmlspecialchars($rol) ?>"><?= htmlspecialchars($rol) ?></span>)</p>

    <p><a href="?logout=1">Cerrar sesión</a></p>

    <div class="panel">
        <?php if ($rol === "legendario"): ?>
            <h2>Panel Legendario</h2>
            <ul>
                <li>Acceso total al sistema</li>
                <li>Configurar panel</li>
                <li>Ver todos los informes</li>
                <li>Administrar Pokémon</li>
            </ul>

        <?php elseif ($rol === "lider_gimnasio"): ?>
            <h2>Panel de Líder de Gimnasio</h2>
            <ul>
                <li>Gestionar combates</li>
                <li>Ver entrenadores registrados</li>
                <li>Acceso parcial a informes</li>
            </ul>

        <?php elseif ($rol === "entrenador"): ?>
            <h2>Panel de Entrenador</h2>
            <ul>
                <li>Ver tus Pokémon</li>
                <li>Participar en torneos</li>
                <li>Acceso a entrenamiento</li>
            </ul>

        <?php elseif ($rol === "guardian"): ?>
            <h2>Panel de Guardián</h2>
            <ul>
                <li>Controlar acceso a zonas seguras</li>
                <li>Supervisar descansos</li>
            </ul>

        <?php elseif ($rol === "novato"): ?>
            <h2>Panel de Novato</h2>
            <ul>
                <li>Aprender habilidades básicas</li>
                <li>Acceso al centro Pokémon</li>
            </ul>
        <?php endif; ?>
    </div>

<?php endif; ?>

</body>
</html>
