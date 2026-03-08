<?php
$Nombre=$_GET["q"];
$Pas=$_GET["p"];
$a=0;

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$entradas = $con->query("SELECT * FROM Usuarios");

while ($fila = $entradas->fetch_assoc())
{
    if($fila['Nombre']==$Nombre && $fila['Clave']==$Pas)
        $a=$fila['Nombre'];
}

echo $a;
?>
