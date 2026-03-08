<?php
$usuario=$_GET["p"];
$clave=$_GET["q"];
$retorno=0;

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "bbddMRCA", "hala;madrid;13", "mrc_2019");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sql='SELECT * FROM users WHERE `username`=\''.$usuario.'\'';
$entradas = $con->query($sql);
$numcoincidencias=$entradas->num_rows;
if($numcoincidencias!=0)
{
	$usuariobbdd = $entradas->fetch_assoc();
	if(password_verify ($clave , $usuariobbdd["password"]))
		$retorno=1;
}

echo $retorno;
?>
