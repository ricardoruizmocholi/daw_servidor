<?php
/*
 * EJ15 — Fragmentos HTML dinámicos generados en servidor
 *
 * Este script NO devuelve JSON — devuelve HTML directamente.
 * El cliente lo recibe con response.text() y lo inyecta con innerHTML.
 *
 * Parámetros GET:
 *   - genero: "accion" | "rpg" | "aventura" | "" (todos)
 *   - orden:  "nombre" | "precio" | "nota"
 *   - vista:  "tarjetas" | "tabla"
 *
 * Los datos vienen del fichero ej15_videojuegos.json (ya creado, en esta misma carpeta)
 */
header("Content-Type: text/html; charset=utf-8");

// TODO 1: Lee los parámetros GET con valores por defecto
//   $genero = $_GET["genero"] ?? "";
//   $orden  = $_GET["orden"]  ?? "nombre";
//   $vista  = $_GET["vista"]  ?? "tarjetas";

// TODO 2: Lee el fichero ej15_videojuegos.json
//   $json = file_get_contents(__DIR__ . "/ej15_videojuegos.json");
//   $juegos = json_decode($json, true);

// TODO 3: Filtra por género si $genero no está vacío
//   Usa array_filter() + array_values()

// TODO 4: Ordena con usort() según $orden
//   usort($juegos, function($a, $b) use ($orden) {
//       return $orden === "precio" || $orden === "nota"
//           ? $a[$orden] <=> $b[$orden]
//           : strcmp($a[$orden], $b[$orden]);
//   });

// TODO 5: Genera el HTML según $vista
//   Si $vista === "tarjetas": genera <div class="tarjeta"> por cada juego
//     con: nombre, genero, precio (€), nota (/10)
//
//   Si $vista === "tabla": genera una <table> con columnas nombre | genero | precio | nota
//
//   Si no hay juegos tras el filtro: devuelve <p>No hay juegos para este filtro.</p>
?>
