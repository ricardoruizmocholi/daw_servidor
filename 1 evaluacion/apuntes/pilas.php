<?php
/*
============================================================
 PILAS EN PHP
============================================================
Definición:
- Una pila (stack) es una estructura de datos LIFO (Last In, First Out).
- El último elemento en entrar es el primero en salir.
- PHP no tiene una estructura "Stack" nativa, pero se puede simular fácilmente con arrays.
- Las funciones más comunes son:
    • array_push() → insertar elemento al final.
    • array_pop()  → eliminar y devolver el último elemento.

Ventajas:
- Muy útil para manejo de historial (por ejemplo, navegación "atrás"), deshacer acciones, etc.

============================================================
 EJEMPLO BÁSICO
============================================================
*/

$pila = []; // pila vacía

array_push($pila, "HTML");
array_push($pila, "CSS");
array_push($pila, "JavaScript");

echo "Pila inicial:\n";
print_r($pila);

// Sacamos elementos (LIFO)
$ultimo = array_pop($pila);
echo "\nElemento eliminado: $ultimo\n";

echo "Pila actual:\n";
print_r($pila);

/*
============================================================
 EJEMPLO 2: SIMULACIÓN DE UN HISTORIAL DE NAVEGACIÓN
============================================================
*/

$historial = [];

function visitarPagina($url) {
    global $historial;
    array_push($historial, $url);
    echo "Has visitado: $url<br>";
}

function volverAtras() {
    global $historial;
    if (!empty($historial)) {
        $ultima = array_pop($historial);
        echo "Volviendo desde: $ultima<br>";
    } else {
        echo "No hay más páginas en el historial.<br>";
    }
}

visitarPagina("index.php");
visitarPagina("productos.php");
visitarPagina("contacto.php");

volverAtras();
volverAtras();

/*
============================================================
 EJEMPLO 3: USO DE PILA PARA EXPRESIONES MATEMÁTICAS
============================================================
Evaluación de una expresión en notación postfija.
Ejemplo: 3 4 + 2 *  → (3+4)*2 = 14
*/

function evaluarPostfija($expresion) {
    $pila = [];
    foreach ($expresion as $token) {
        if (is_numeric($token)) {
            array_push($pila, $token);
        } else {
            $b = array_pop($pila);
            $a = array_pop($pila);
            switch ($token) {
                case '+': array_push($pila, $a + $b); break;
                case '-': array_push($pila, $a - $b); break;
                case '*': array_push($pila, $a * $b); break;
                case '/': array_push($pila, $a / $b); break;
            }
        }
    }
    return array_pop($pila);
}

echo "<br>Resultado postfijo: " . evaluarPostfija([3, 4, '+', 2, '*']); // 14
?>
