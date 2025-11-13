<?php
/*
============================================================
 COOKIES EN PHP
============================================================
Definición:
- Las cookies son pequeños archivos de texto que el servidor envía al navegador del usuario.
- El navegador las guarda y las reenvía al servidor en cada solicitud posterior.
- Se usan para recordar información entre visitas (por ejemplo, idioma, carrito, preferencias).

Importante:
- Los datos se guardan en el navegador del usuario.
- Tienen fecha de caducidad.
- No se deben almacenar datos sensibles sin cifrar.

============================================================
 SINTAXIS:
============================================================
setcookie(nombre, valor, tiempo_expiracion, ruta, dominio, seguro, httponly);

Parámetros:
- nombre → nombre de la cookie.
- valor → dato que queremos guardar.
- tiempo_expiracion → timestamp (time() + segundos).
- ruta → ruta del servidor donde es válida.
- dominio → dominio donde es válida.
- seguro → true si solo debe enviarse por HTTPS.
- httponly → true para impedir acceso desde JavaScript (mejor seguridad).

============================================================
 EJEMPLO 1: CREAR UNA COOKIE SIMPLE
============================================================
*/

// Crear cookie que dure 1 hora
setcookie("usuario", "Ricardo", time() + 3600);

if (isset($_COOKIE['usuario'])) {
    echo "Bienvenido de nuevo, " . $_COOKIE['usuario'] . "<br>";
} else {
    echo "Cookie 'usuario' aún no creada o expirada.<br>";
}

/*
============================================================
 EJEMPLO 2: COOKIE CON FECHA DE EXPIRACIÓN PERSONALIZADA
============================================================
*/

$duracion = 7 * 24 * 60 * 60; // 7 días
setcookie("tema", "oscuro", time() + $duracion);

echo "Cookie 'tema' configurada a 'oscuro' por una semana.<br>";

/*
============================================================
 EJEMPLO 3: ELIMINAR UNA COOKIE
============================================================
Para eliminar una cookie, hay que volver a crearla con una fecha expirada.
*/

setcookie("tema", "", time() - 3600); // caducada
echo "Cookie 'tema' eliminada.<br>";

/*
============================================================
 EJEMPLO 4: COOKIES SEGURAS
============================================================
- Se envían solo por HTTPS y no pueden ser accedidas por JavaScript.
- Recomendado para datos sensibles (aunque cifrados).
*/

setcookie(
    "session_token",
    "abc123xyz",
    [
        "expires" => time() + 3600,
        "path" => "/",
        "secure" => true,       // solo HTTPS
        "httponly" => true,     // no accesible desde JS
        "samesite" => "Strict"  // evita envío en peticiones externas
    ]
);

/*
============================================================
 EJEMPLO 5: COOKIE DE SESIÓN (sin expiración)
============================================================
- Dura mientras el navegador esté abierto.
- No se le pasa parámetro 'expires'.
*/

setcookie("idioma", "español");
echo "Cookie de sesión 'idioma' creada.<br>";

/*
============================================================
 EJEMPLO 6: GUARDAR PREFERENCIAS DE USUARIO
============================================================
*/

if (!isset($_COOKIE['color_fondo'])) {
    setcookie("color_fondo", "#a2d5f2", time() + 86400 * 30); // 30 días
    echo "Cookie 'color_fondo' creada con valor por defecto.<br>";
} else {
    echo "Color de fondo preferido: " . $_COOKIE['color_fondo'] . "<br>";
}

/*
============================================================
 EJEMPLO 7: CÓMO MOSTRAR TODAS LAS COOKIES
============================================================
*/

echo "<hr><h3>Todas las cookies recibidas:</h3>";
foreach ($_COOKIE as $nombre => $valor) {
    echo "$nombre = $valor<br>";
}

/*
============================================================
 BUENAS PRÁCTICAS DE SEGURIDAD
============================================================
✔ No guardar contraseñas ni tokens sin cifrar.
✔ Usar 'secure' y 'httponly' siempre que sea posible.
✔ Usar SameSite="Strict" o "Lax" para evitar ataques CSRF.
✔ Validar el contenido de las cookies antes de usarlas.
✔ Evitar cookies demasiado grandes (el límite es ~4 KB).
*/

?>
