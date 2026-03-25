

/*
///////////////////////////////////////
//            Fetch GET              //
///////////////////////////////////////


Se usa cuando solo quieres pedir datos al servidor. Los parámetros van visibles en la URL.

Uso: Buscadores, listados generales o ver un perfil por ID.

En PHP: Se reciben mediante $_GET.

*/
async function fetchGet() {
  // Opción A: URL estática
  // Opción B: Usar URLSearchParams para construir la URL limpiamente
  const params = new URLSearchParams({
    id: 10,
    categoria: "rpg"
  });

  try {
    const resp = await fetch(`servidor.php?${params.toString()}`);
    const data = await resp.json();
    console.log(data);
  } catch (error) {
    console.error("Error en GET:", error);
  }
}



///////////////////////////////////////
//      Fetch POST con JSON          //
///////////////////////////////////////
/*

    Este es el que me pediste como prioridad. Envía un objeto complejo empaquetado como texto.

Uso: Crear registros nuevos, filtros avanzados, login.

En PHP: NO se usa $_POST. Se usa file_get_contents("php://input").

*/

async function fetchPostJSON() {
  const misDatos = { titulo: "Zelda", precio: 59.99 };

  try {
    const resp = await fetch("servidor.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json" // Indispensable para que el servidor sepa qué recibe
      },
      body: JSON.stringify(misDatos) // Convertimos el objeto a texto JSON
    });

    const data = await resp.json(); // Recibimos el JSON de vuelta convertido a objeto JS
    console.log(data);
  } catch (error) {
    console.error("Error en POST JSON:", error);
  }
}

///////////////////////////////////////
//      Fetch POST con FormData      //
///////////////////////////////////////

/*

Simula el envío de un formulario de toda la vida <form>.

Uso: Cuando el profesor pide específicamente usar $_POST en PHP o si tienes que enviar archivos/imágenes.

En PHP: Se reciben mediante $_POST.

Nota: NO pongas cabecera Content-Type, el navegador la pone solo.

*/

async function fetchPostFormData() {
  const fd = new FormData();
  fd.append("titulo", "Mario Odyssey");
  fd.append("precio", 45);

  try {
    const resp = await fetch("servidor.php", {
      method: "POST",
      body: fd // Enviamos el objeto FormData directamente
    });

    const data = await resp.json();
    console.log(data);
  } catch (error) {
    console.error("Error en FormData:", error);
  }
}

///////////////////////////////////////
//      Fetch PUT / DELETE           //
///////////////////////////////////////

/*
    Se usan en arquitecturas API REST. Son hermanos del POST, pero semánticamente indican "Actualizar" o "Borrar".

En PHP: Para leer los datos de un PUT, se suele usar el mismo método que con JSON (php://input).

*/

// Ejemplo DELETE
async function borrarJuego(id) {
  const resp = await fetch(`servidor.php?id=${id}`, {
    method: "DELETE"
  });
  const data = await resp.json();
}

// Ejemplo PUT (Actualizar todo el objeto)
async function actualizarJuego(datos) {
  const resp = await fetch("servidor.php", {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(datos)
  });
  const data = await resp.json();
}

///////////////////////////////////////
//      (Manejo de Errores)          //
///////////////////////////////////////
/*

    En el examen te dará puntos extra (o te quitará si no lo pones) manejar si el servidor dio error (ej. error 500 o 404).

*/

async function fetchSeguro() {
  const resp = await fetch("api.php");

  // .ok es true si el código es 200-299
  if (!resp.ok) {
    throw new Error("El servidor ha fallado con código: " + resp.status);
  }

  const data = await resp.json();
  return data;
}