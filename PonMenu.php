<?php
$opcion=$_GET["p"];

error_reporting(E_ALL ^ E_DEPRECATED);

$colorfondo[0]='';
$colorfondo[1]='';
$colorfondo[2]='';
$colorfondo[3]='';
$colorfondo[$opcion]='style="background:#3663a7;color:white;"';

$stropcion[0]='INSTALACIÓN';
$stropcion[1]='REGISTRO SONORO';
$stropcion[2]='CONFIGURACIÓN';
$stropcion[3]='USUARIOS';
$display[0]='contents';
$display[1]='contents';
$display[2]='contents';
$display[3]='contents';
$display[$opcion]='none';

//Se ponen las pestañas según los privilegios y la opción
	//Primero para menú de pantallas normales
$strHTML='
	<li '.$colorfondo[0].'>
		<a id="TRA_MENUTAG0" onclick="PonMenuHTM(0,-1)">INSTALACIÓN</a>
	</li>
	<li '.$colorfondo[1].'>
		<a id="TRA_MENUTAG1" onclick="PonMenuHTM(1,-1)">REGISTRO SONORO</a>
	</li>
	<li '.$colorfondo[2].'>
		<a id="TRA_MENUTAG2" onclick="PonMenuHTM(2,-1)">CONFIGURACIÓN</a>
	</li>
	<li '.$colorfondo[3].'>
		<a id="TRA_MENUTAG3" onclick="PonMenuHTM(3,-1)">USUARIOS</a>
	</li>
';
$strHTML.='~';
	//Y ahora para menú de pantallas pequeñas (menú hamburguesa).
$strHTML.='
	<div style="position:absolute;top:-1px;left:50px;height:25px;width:88%;background-color:#3663a7;"> 
		<p id="TRA_OpPeqSelec" style="position:absolute;top:-19px;margin-left:5px;font-family:Verdana;font-size:20px;color:white;">'.$stropcion[$opcion].'</p>
	</div>
	<input type="checkbox"/>
	<span></span>
	<span></span>
	<span></span>
	<ul id="ListaMenuPequeno">
		<a onclick="PonMenuHTM(0,-1)" style="display:'.$display[0].'"><li id="TRA_OpPeq1">INSTALACIÓN</li></a>
		<a onclick="PonMenuHTM(1,-1)" style="display:'.$display[1].'"><li id="TRA_OpPeq1">REGISTRO SONORO</li></a>
		<a onclick="PonMenuHTM(2,-1)" style="display:'.$display[2].'"><li id="TRA_OpPeq2">CONFIGURACIÓN</li></a>
		<a onclick="PonMenuHTM(3,-1)" style="display:'.$display[3].'"><li id="TRA_OpPeq3">USUARIOS</li></a>
	</ul>
';
echo $strHTML;
?>
