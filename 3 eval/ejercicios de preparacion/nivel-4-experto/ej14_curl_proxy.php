<?php
/*
 * EJ14 — Servidor intermediario con cURL
 *
 * El cliente pide noticias a ESTE servidor.
 * Este servidor las va a buscar al "proveedor externo" (ej14_noticias_externas.php)
 * y devuelve el resultado transformado al cliente.
 *
 * Parámetro GET: ?categoria=X
 * Categorías válidas: "tecnologia", "deportes", "cultura"
 * Si la categoría no es válida → 400 + JSON de error
 *
 * URL del proveedor externo: "http://localhost/TU_RUTA/ej14_noticias_externas.php?categoria=X"
 * (ajusta la ruta a tu configuración de Apache)
 *
 * La respuesta del proveedor externo es un array JSON:
 * [{ "titulo": "...", "fecha": "...", "fuente": "..." }, ...]
 *
 * Este servidor debe transformarlos y devolver:
 * { "categoria": "...", "total": N, "noticias": [...] }
 */
header("Content-Type: application/json; charset=utf-8");

$categoriasValidas = ["tecnologia", "deportes", "cultura"];

// TODO 1: Lee $_GET["categoria"]
//         Si no existe o no está en $categoriasValidas → http_response_code(400) + json + exit

// TODO 2: Construye la URL del proveedor externo con la categoría
//         $urlExterna = "http://localhost/TU_RUTA/ej14_noticias_externas.php?categoria=" . urlencode($categoria);

// TODO 3: Usa cURL para hacer GET a esa URL
//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $urlExterna);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   ← sin esto, cURL imprime en pantalla
//   $respuesta = curl_exec($ch);
//   curl_close($ch);

// TODO 4: Decodifica la respuesta JSON del proveedor con json_decode($respuesta, true)
//         Si el resultado es null → http_response_code(502) + error JSON

// TODO 5: Devuelve 200 con el JSON transformado:
//   { "categoria": $categoria, "total": count(...), "noticias": [...] }
?>
