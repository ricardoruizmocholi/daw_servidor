<?php
/*
============================================================
 COLAS EN PHP
============================================================
Definición:
- Una cola (queue) es una estructura FIFO (First In, First Out).
- El primer elemento en entrar es el primero en salir.
- PHP tampoco tiene una estructura nativa para colas, pero se puede simular con arrays.
- Funciones comunes:
    • array_push()   → añadir al final (enqueue)
    • array_shift()  → eliminar del principio (dequeue)

Aplicaciones:
- Gestión de tareas pendientes.
- Colas de impresión.
- Sistemas de mensajería.
*/

$cola = [];

array_push($cola, "Cliente 1");
array_push($cola, "Cliente 2");
array_push($cola, "Cliente 3");

echo "Cola inicial:\n";
print_r($cola);

// Atiendo al primer cliente
$atendido = array_shift($cola);
echo "\nAtendiendo a: $atendido\n";
print_r($cola);

/*
============================================================
 EJEMPLO 2: SIMULACIÓN DE COLA DE TAREAS
============================================================
*/

$tareas = [];

function añadirTarea($nombre) {
    global $tareas;
    array_push($tareas, $nombre);
    echo "Tarea añadida: $nombre<br>";
}

function procesarTarea() {
    global $tareas;
    if (!empty($tareas)) {
        $tarea = array_shift($tareas);
        echo "Procesando tarea: $tarea<br>";
    } else {
        echo "No hay tareas pendientes.<br>";
    }
}

añadirTarea("Enviar correo");
añadirTarea("Generar informe");
añadirTarea("Limpiar caché");

procesarTarea();
procesarTarea();

/*
============================================================
 EJEMPLO 3: COLA CON SPLQUEUE (CLASE NATIVA)
============================================================
- PHP sí ofrece la clase SplQueue (parte de SPL - Standard PHP Library)
- Proporciona una estructura FIFO real con métodos enqueue() y dequeue().
*/

$colaSPL = new SplQueue();

$colaSPL->enqueue("Mensaje 1");
$colaSPL->enqueue("Mensaje 2");

echo "<br>Elemento sacado de la cola SPL: " . $colaSPL->dequeue() . "<br>";

foreach ($colaSPL as $elemento) {
    echo "Elemento restante: $elemento<br>";
}
?>
