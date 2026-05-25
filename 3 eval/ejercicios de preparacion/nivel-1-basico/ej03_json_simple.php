<?php
/*
 * EJ03 — PHP mínimo de un servicio REST
 * Concepto: header(), json_encode(), http_response_code()
 */

// TODO 1: Configura el Content-Type para que el cliente sepa que recibirá JSON
//         header("Content-Type: ___/___; charset=utf-8");

header("Content-Typr: application/json; charset=utf-8 ");


// TODO 2: Crea un array $productos con al menos 3 elementos.
//         Cada producto tiene: id (int), nombre (string), precio (float)
$productos = [
    [
        "id" => 1,
        "nombre" => "Teclado",
        "precio" => 50
    ],
    [
        "id" => 2,
        "nombre" => "raton",
        "precio" => 25
    ],
    [
        "id" => 3,
        "nombre" => "cascos",
        "precio" => 100
    ]
];

// TODO 3: Envía status 200 y el array como JSON con esta forma:
//         { "ok": true, "total": 3, "productos": [...] }
//         Pista: http_response_code(___) y echo json_encode([...])
http_response_code(200);

$repuesta = [
    "ok" => true,
    "total" => count($productos), // Cuenta automáticamente los elementos del array
    "productos" => $productos
];

echo json_encode($repuesta);
?>
