<?php
$nombre=$_GET["q"];
$claveant=$_GET["p"];
$clavenou=$_GET["r"];
$retorno=0;

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sel = $con->query("SELECT * FROM Usuarios WHERE Nombre = '".$nombre."'");
$fila=$sel->fetch_assoc();

if($fila['Clave']==$claveant)
{
	$con->query("UPDATE Usuarios SET Clave = '".$clavenou."' WHERE Nombre = '".$nombre."'");
	$retorno=1;
}
echo $retorno;
?>
