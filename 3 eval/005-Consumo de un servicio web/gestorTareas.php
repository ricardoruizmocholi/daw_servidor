<?php
/*
 * OBJETIVO DEL EJERCICIO: Implementar una API REST completa de gestión de tareas
 *                         con operaciones CRUD y almacenamiento en fichero JSON.
 * CONCEPTO QUE ENTRENA: Diseño REST (recursos + verbos HTTP + status codes semánticos),
 *                       lectura del body con php://input, PATH_INFO para leer IDs en la URL.
 * PARA EL EXAMEN: La ruta base es /tareas; el ID opcional va después: /tareas/5.
 *                 Cada método HTTP tiene su status code correcto:
 *                   GET → 200, POST → 201, PATCH → 200, DELETE → 204, error → 400/404/405
 */
header("Content-Type: application/json; charset=utf-8");

// ─────────────────────────────────────────────────────────────────────────────
// Almacenamiento: fichero JSON en el mismo directorio que este script
// En un proyecto real sería una base de datos — aquí usamos JSON para centrarnos
// en el protocolo REST sin añadir complejidad de BD.
// ─────────────────────────────────────────────────────────────────────────────
$archivoDatos = __DIR__ . "/tareas.json";

// Si el fichero no existe todavía, lo creamos con algunos datos de ejemplo.
// Así el servicio funciona desde el primer momento sin configuración manual.
if (!file_exists($archivoDatos)) {
    $tareasIniciales = [
        [
            "id"           => 1,
            "titulo"       => "Estudiar protocolo REST",
            "descripcion"  => "Repasar métodos HTTP, status codes y diseño de recursos",
            "completada"   => true,
            "prioridad"    => "alta",
            "fechaCreacion" => "2025-01-15"
        ],
        [
            "id"           => 2,
            "titulo"       => "Practicar SOAP con WSDL",
            "descripcion"  => "Entender la estructura del Envelope XML y el rol del WSDL",
            "completada"   => false,
            "prioridad"    => "alta",
            "fechaCreacion" => "2025-01-16"
        ],
        [
            "id"           => 3,
            "titulo"       => "Crear servicio con headers SOAP",
            "descripcion"  => "Implementar un Header de sesión en el servicio de préstamo",
            "completada"   => false,
            "prioridad"    => "media",
            "fechaCreacion" => "2025-01-17"
        ]
    ];

    file_put_contents(
        $archivoDatos,
        json_encode($tareasIniciales, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// Funciones auxiliares
// ─────────────────────────────────────────────────────────────────────────────

// Lee el fichero JSON y devuelve el array de tareas
function leerTareas($archivoDatos) {
    $contenido = file_get_contents($archivoDatos);
    return json_decode($contenido, true) ?? [];
}

// Guarda el array de tareas en el fichero JSON
function guardarTareas($archivoDatos, $tareas) {
    file_put_contents(
        $archivoDatos,
        json_encode(array_values($tareas), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

// Envía la respuesta JSON con el status code correcto y termina el script
function responder($codigo, $datos = null) {
    http_response_code($codigo);

    if ($datos !== null) {
        echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    exit;
}

// Lee y parsea el body JSON de la petición (para POST y PATCH)
// Si el body no es JSON válido, responde con 400 Bad Request
function leerJSONBody() {
    $raw  = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if ($raw !== "" && $data === null) {
        responder(400, ["error" => "El cuerpo de la petición no es JSON válido."]);
    }

    return $data ?? [];
}

// ─────────────────────────────────────────────────────────────────────────────
// Leer el método HTTP y la ruta
// ─────────────────────────────────────────────────────────────────────────────

$metodo = $_SERVER["REQUEST_METHOD"];

// PATH_INFO contiene la parte de la URL después del nombre del script.
// Ejemplo: si la URL es gestorTareas.php/tareas/3 → PATH_INFO es /tareas/3
// Si la URL es gestorTareas.php/tareas          → PATH_INFO es /tareas
$ruta = $_SERVER["PATH_INFO"] ?? "";

// Limpiamos barras iniciales y finales y separamos los segmentos
$partes  = explode("/", trim($ruta, "/"));
$recurso = $partes[0] ?? ""; // "tareas"
$id      = $partes[1] ?? null; // "3" o null si no hay ID

// Solo gestionamos el recurso "tareas"
if ($recurso !== "tareas") {
    responder(404, [
        "error" => "Recurso no encontrado. Usa /tareas o /tareas/{id}."
    ]);
}

$tareas = leerTareas($archivoDatos);

// ─────────────────────────────────────────────────────────────────────────────
// GET /tareas
// Devuelve todas las tareas
// ─────────────────────────────────────────────────────────────────────────────
if ($metodo === "GET" && $id === null) {
    // 200 OK — la petición ha funcionado y hay datos que devolver
    responder(200, [
        "total"  => count($tareas),
        "tareas" => $tareas
    ]);
}

// ─────────────────────────────────────────────────────────────────────────────
// GET /tareas/{id}
// Devuelve una tarea concreta por su ID
// ─────────────────────────────────────────────────────────────────────────────
if ($metodo === "GET" && $id !== null) {
    foreach ($tareas as $tarea) {
        if ($tarea["id"] == $id) {
            responder(200, $tarea); // 200 OK — tarea encontrada
        }
    }

    responder(404, ["error" => "No existe ninguna tarea con id=$id."]); // 404 Not Found
}

// ─────────────────────────────────────────────────────────────────────────────
// POST /tareas
// Crea una tarea nueva con los datos del body
// ─────────────────────────────────────────────────────────────────────────────
if ($metodo === "POST" && $id === null) {
    $data = leerJSONBody();

    // Validación: el título es obligatorio — sin él no tiene sentido la tarea
    $titulo = trim($data["titulo"] ?? "");
    if ($titulo === "") {
        responder(400, ["error" => "El campo 'titulo' es obligatorio."]);
    }

    // Los valores opcionales tienen valores por defecto sensatos
    $descripcion   = trim($data["descripcion"] ?? "");
    $completada    = isset($data["completada"]) ? (bool)$data["completada"] : false;
    $prioridad     = trim($data["prioridad"]    ?? "media");
    $fechaCreacion = date("Y-m-d"); // La fecha la asigna el servidor, no el cliente

    // Validación del campo prioridad — solo acepta valores conocidos
    $prioridadesValidas = ["alta", "media", "baja"];
    if (!in_array($prioridad, $prioridadesValidas, true)) {
        responder(400, [
            "error" => "El campo 'prioridad' debe ser 'alta', 'media' o 'baja'."
        ]);
    }

    // Generamos el nuevo ID como el máximo actual + 1 (igual que un AUTO_INCREMENT de SQL)
    $ids    = array_column($tareas, "id");
    $nuevoId = empty($ids) ? 1 : max($ids) + 1;

    $nuevaTarea = [
        "id"           => $nuevoId,
        "titulo"       => $titulo,
        "descripcion"  => $descripcion,
        "completada"   => $completada,
        "prioridad"    => $prioridad,
        "fechaCreacion" => $fechaCreacion
    ];

    $tareas[] = $nuevaTarea;
    guardarTareas($archivoDatos, $tareas);

    // 201 Created — recurso creado correctamente; devolvemos la tarea creada
    responder(201, $nuevaTarea);
}

// ─────────────────────────────────────────────────────────────────────────────
// PATCH /tareas/{id}
// Actualiza solo los campos que se envían (actualización parcial)
// ─────────────────────────────────────────────────────────────────────────────
if ($metodo === "PATCH" && $id !== null) {
    $data = leerJSONBody();

    foreach ($tareas as $indice => $tarea) {
        if ($tarea["id"] == $id) {

            // Solo actualizamos los campos que el cliente ha enviado explícitamente.
            // Los que no vienen en el body se quedan igual — eso es la definición de PATCH.
            if (isset($data["titulo"])) {
                $nuevoTitulo = trim($data["titulo"]);
                if ($nuevoTitulo === "") {
                    responder(400, ["error" => "El campo 'titulo' no puede quedar vacío."]);
                }
                $tareas[$indice]["titulo"] = $nuevoTitulo;
            }

            if (isset($data["descripcion"])) {
                $tareas[$indice]["descripcion"] = trim($data["descripcion"]);
            }

            if (isset($data["completada"])) {
                $tareas[$indice]["completada"] = (bool)$data["completada"];
            }

            if (isset($data["prioridad"])) {
                $prioridadesValidas = ["alta", "media", "baja"];
                if (!in_array($data["prioridad"], $prioridadesValidas, true)) {
                    responder(400, [
                        "error" => "El campo 'prioridad' debe ser 'alta', 'media' o 'baja'."
                    ]);
                }
                $tareas[$indice]["prioridad"] = $data["prioridad"];
            }

            guardarTareas($archivoDatos, $tareas);

            // 200 OK — devolvemos la tarea actualizada para que el cliente vea el estado final
            responder(200, $tareas[$indice]);
        }
    }

    responder(404, ["error" => "No existe ninguna tarea con id=$id."]);
}

// ─────────────────────────────────────────────────────────────────────────────
// DELETE /tareas/{id}
// Elimina una tarea y devuelve 204 (éxito sin cuerpo)
// ─────────────────────────────────────────────────────────────────────────────
if ($metodo === "DELETE" && $id !== null) {
    foreach ($tareas as $indice => $tarea) {
        if ($tarea["id"] == $id) {
            array_splice($tareas, $indice, 1);
            guardarTareas($archivoDatos, $tareas);

            // 204 No Content — la operación fue exitosa pero no hay nada que devolver
            // (el recurso ya no existe, no tiene sentido devolverlo)
            responder(204);
        }
    }

    responder(404, ["error" => "No existe ninguna tarea con id=$id."]);
}

// ─────────────────────────────────────────────────────────────────────────────
// Cualquier otra combinación método+ruta que no hayamos contemplado
// ─────────────────────────────────────────────────────────────────────────────
// 405 Method Not Allowed — el método HTTP no está soportado para esta ruta
responder(405, [
    "error"   => "Método '$metodo' no permitido para esta ruta.",
    "metodos" => ["GET /tareas", "GET /tareas/{id}", "POST /tareas", "PATCH /tareas/{id}", "DELETE /tareas/{id}"]
]);
?>
