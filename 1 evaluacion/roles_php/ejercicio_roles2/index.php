<?php
session_start();

// Usuarios estáticos (podrían ir en un archivo aparte)
$usuarios = [
    ["usuario" => "pikachu", "password" => "trueno", "rol" => "entrenador"],
    ["usuario" => "charizard", "password" => "fuego", "rol" => "lider_gimnasio"],
    ["usuario" => "mewtwo", "password" => "psiquico", "rol" => "legendario"],
    ["usuario" => "bulbasaur", "password" => "hoja", "rol" => "novato"],
    ["usuario" => "snorlax", "password" => "zzz", "rol" => "guardian"]
];

// Si ya hay sesión activa, ir directo a panel.php
/*

if (isset($_SESSION["usuario"])) {
    header("Location: panel.php");
    exit;
}
*/

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["usuario"], $_POST["contraseña"])) {
    $usuario = trim($_POST["usuario"]);
    $clave = trim($_POST["contraseña"]);

    foreach ($usuarios as $u) {
        if ($u["usuario"] === $usuario && $u["password"] === $clave) {
            // Guardar datos en sesión
            $_SESSION["usuario"] = $u["usuario"];
            $_SESSION["rol"] = $u["rol"];
            $_SESSION["password"] = $u["password"];
            header("Location: panel.php");
            exit;
        }
    }
    $error = "Usuario o contraseña incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login Pokémon</title>
</head>
<body>

<h1>Login Pokémon</h1>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="">
    <label>Usuario:</label><br>
    <input type="text" name="usuario" required><br><br>
    <label>Contraseña:</label><br>
    <input type="password" name="contraseña" required><br><br>
    <button type="submit">Entrar</button>
</form>

</body>
</html>
