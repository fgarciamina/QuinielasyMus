<?php

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sel=$con->query("SELECT * FROM Jornada");
$jornada=$sel->fetch_assoc();
$jornadanueva=$jornada['Jornadaencurso'];
$jornadanueva++;
$con->query("UPDATE Jornada SET Jornadaencurso=".$jornadanueva." WHERE 1");

echo $jornadanueva;
?>
