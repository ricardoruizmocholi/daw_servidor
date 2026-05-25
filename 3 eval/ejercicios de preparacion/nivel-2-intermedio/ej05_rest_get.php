<?php
/*
 * EJ05 — Endpoint REST GET con filtro opcional
 * Concepto: $_GET, array_filter(), array_values(), json_encode()
 * Prueba en el navegador:
 *   - ej05_rest_get.php            → todas las películas
 *   - ej05_rest_get.php?genero=drama → solo las de drama
 */

// TODO 1: header Content-Type JSON

$peliculas = [
    ["id" => 1, "titulo" => "El Padrino",              "genero" => "drama",           "anio" => 1972, "nota" => 9.2],
    ["id" => 2, "titulo" => "Pulp Fiction",            "genero" => "crimen",          "anio" => 1994, "nota" => 8.9],
    ["id" => 3, "titulo" => "El Señor de los Anillos", "genero" => "fantasia",        "anio" => 2001, "nota" => 8.8],
    ["id" => 4, "titulo" => "Gladiator",               "genero" => "drama",           "anio" => 2000, "nota" => 8.5],
    ["id" => 5, "titulo" => "Interstellar",            "genero" => "ciencia-ficcion", "anio" => 2014, "nota" => 8.6],
];

// TODO 2: Lee $_GET["genero"] (si existe)
//         Si existe, filtra $peliculas por ese género
//         Pista: array_filter() + array_values()

// TODO 3: Devuelve 200 con { "total": N, "filtro": X|null, "peliculas": [...] }
?>
