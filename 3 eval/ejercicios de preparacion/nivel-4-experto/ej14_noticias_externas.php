<?php
/*
 * Proveedor externo simulado para ej14_curl_proxy.php
 * Este archivo NO se toca — simula una API externa de terceros.
 */
header("Content-Type: application/json; charset=utf-8");

$categoria = $_GET["categoria"] ?? "general";

$noticias = [
    "tecnologia" => [
        ["titulo" => "PHP 9 anuncia soporte nativo para tipos de unión", "fecha" => "2025-05-20", "fuente" => "PHPWeekly"],
        ["titulo" => "JavaScript supera a Python en popularidad en encuestas", "fecha" => "2025-05-19", "fuente" => "DevSurvey"],
        ["titulo" => "Apache HTTP Server lanza versión 2.6 con mejoras de rendimiento", "fecha" => "2025-05-18", "fuente" => "ServerWorld"],
    ],
    "deportes" => [
        ["titulo" => "España gana el campeonato europeo de baloncesto", "fecha" => "2025-05-21", "fuente" => "SportES"],
        ["titulo" => "Nuevo récord mundial en los 100 metros lisos", "fecha" => "2025-05-20", "fuente" => "AthleticsNow"],
        ["titulo" => "La Liga española cierra la temporada con récord de audiencia", "fecha" => "2025-05-19", "fuente" => "FutbolHoy"],
    ],
    "cultura" => [
        ["titulo" => "El Museo del Prado inaugura exposición sobre el Renacimiento", "fecha" => "2025-05-22", "fuente" => "ArteES"],
        ["titulo" => "Premio Planeta otorgado a joven escritora catalana", "fecha" => "2025-05-20", "fuente" => "Libros21"],
        ["titulo" => "Festival de Cine de San Sebastián anuncia su programación", "fecha" => "2025-05-18", "fuente" => "CineEuropa"],
    ],
];

$resultado = $noticias[$categoria] ?? [];

echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
