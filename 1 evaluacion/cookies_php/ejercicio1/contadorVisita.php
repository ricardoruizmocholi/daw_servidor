<?php
// contador de visitas con cookies
if (isset($_COOKIE['visitas'])){
    $visitas = $_COOKIE['visitas']++;
}else{
    $visitas = 1;
}
setcookie('visitas',$visitas,time()+ (30 * 24 * 60 * 60), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de visitas</title>
</head>
<body>
    <h1>Contador de visitas con PHP</h1>
  <p>Has visitado esta página <strong><?= $visitas ?></strong> veces.</p>
  <a href="?reset=1">Reiniciar contador</a>

<?php
if (isset($_GET['reset'])){
    setcookie('visitas','',time()-3600,'/');
    echo'<p> Contador reiniciado . Recarga la pagina</p>';
}
?>
</body>
</html>
