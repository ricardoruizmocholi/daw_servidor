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
// Roles y permisos globales con textos personalizados
// ----------------------------------------------------
$todos_los_permisos = [
    "usuarios" => "Administrar Pokémon",
    "contenido" => "Configurar panel",
    "informes" => "Ver todos los informes",
    "configuracion" => "Acceso total al sistema",
    "torneos" => "Participar en torneos",
    "entrenamiento" => "Entrenar Pokémon",
    "captura" => "Capturar Pokémon",
    "seguridad" => "Zona de seguridad",
    "reposo" => "Zona de descanso"
];

// ----------------------------------------------------
// Definición de roles con sus permisos (true=acceso)
// ----------------------------------------------------
$roles = [
    "legendario" => [
        "usuarios" => true,
        "contenido" => true,
        "informes" => true,
        "configuracion" => true,
        "torneos" => true,
        "entrenamiento" => true,
        "captura" => true,
        "seguridad" => true,
        "reposo" => true
    ],
    "lider_gimnasio" => [
        "usuarios" => true,
        "contenido" => true,
        "informes" => true,
        "configuracion" => false,
        "torneos" => true,
        "entrenamiento" => true,
        "captura" => false,
        "seguridad" => true,
        "reposo" => false
    ],
    "entrenador" => [
        "usuarios" => false,
        "contenido" => true,
        "informes" => false,
        "configuracion" => false,
        "torneos" => true,
        "entrenamiento" => true,
        "captura" => true,
        "seguridad" => false,
        "reposo" => false
    ],
    "guardian" => [
        "usuarios" => false,
        "contenido" => false,
        "informes" => true,
        "configuracion" => false,
        "torneos" => false,
        "entrenamiento" => false,
        "captura" => false,
        "seguridad" => true,
        "reposo" => true
    ],
    "novato" => [
        "usuarios" => false,
        "contenido" => false,
        "informes" => false,
        "configuracion" => false,
        "torneos" => false,
        "entrenamiento" => true,
        "captura" => true,
        "seguridad" => false,
        "reposo" => false
    ]
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
            header("Location: " . $_SERVER['PHP_SELF']);
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
    header("Location: " . $_SERVER['PHP_SELF']);
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
    <meta charset="UTF-8" />
    <title>Panel Pokémon con Cookies y Sesiones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { margin-bottom: 20px; }
        input, button { margin: 5px 0; padding: 6px; }
        .panel { border: 1px solid #ccc; padding: 10px; border-radius: 8px; margin-top: 10px; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 8px; }
        a.btn { display: inline-block; padding: 5px 10px; background: #0078d7; color: white; border-radius: 4px; text-decoration: none; }
        a.btn:hover { background: #005fa3; }
        .legendario { color: purple; }
        .entrenador { color: orange; }
        .lider_gimnasio { color: red; }
        .guardian { color: green; }
        .novato { color: gray; }
    </style>
</head>
<body>

<h1>⚙️ Roles Pokémon con Sesiones y Cookies</h1>

<?php if (!$usuario): ?>
    <form method="post" action="">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required /><br><br>
        <label>Contraseña:</label><br>
        <input type="password" name="contraseña" required /><br><br>
        <button type="submit">Entrar</button>
    </form>

<?php else: ?>

    <p>👋 Bienvenido <strong><?= htmlspecialchars($usuario) ?></strong>
    (rol: <span class="<?= htmlspecialchars($rol) ?>"><?= htmlspecialchars($rol) ?></span>)</p>

    <p><a href="?logout=1">Cerrar sesión</a></p>

    <div class="panel">
        <?php if ($rol === "legendario"): ?>
            <h2>Panel Legendario</h2>
            <p><em>Acceso total al sistema</em></p>
        <?php else: ?>
            <h2>Panel <?= ucfirst($rol) ?></h2>
            <pre>
            <?php 
                foreach ($todos_los_permisos as $clave => $texto) {
                    if (!empty($roles[$rol][$clave])) {
                        echo "$clave => $texto\n";
                    }
                }
            ?>
             </pre>
        <?php endif; ?>

        <ul>
            <?php foreach ($todos_los_permisos as $clave => $texto): ?>
                <?php if (!empty($roles[$rol][$clave])): ?>
                  
                    <li><?= htmlspecialchars($texto) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

<?php endif; ?>

</body>
</html>
