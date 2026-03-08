<?php
$signo=$_GET["q"];
$casilla=$_GET["p"];
$nombre=$_GET["r"];
$existe=0;

function NumeroMultiples($signos, $opcion, $comun)
{
	$faltantes=FALSE;
	$dobles=0;
	$triples=0;
	for($b=1;$b<15;$b++)
	{
		switch($b)
		{
			case 10:
				$a="A";
				break;
			case 11:
				$a="B";
				break;
			case 12:
				$a="C";
				break;
			case 13:
				$a="D";
				break;
			case 14:
				$a="E";
				break;
			default:
				$a=$b;
		}
		$aux=0;
		if($signos['P'.$a.'1'.$comun]=='checked')
			$aux++;
		if($signos['P'.$a.'X'.$comun]=='checked')
			$aux++;
		if($signos['P'.$a.'2'.$comun]=='checked')
			$aux++;
		if($aux==0)
			$faltantes=TRUE;
		else if($aux==2)
			$dobles++;
		else if($aux==3)
			$triples++;
	}
	if($signos['PF1'.$comun]=='?' || $signos['PF1'.$comun]=='' || $signos['PF2'.$comun]=='?' || $signos['PF2'.$comun]=='')
		$faltantes=TRUE;	
	
	if($opcion==0)
		return $faltantes;
	else if($opcion==2)
		return $dobles;
	
	return $triples;
}

function Errores($signos, $comun, $numdobles, $numtriples, $ano, $jornada)
{
	if(NumeroMultiples($signos, 0, $comun)==TRUE)
		return "Falta pronóstico en algún partido";
	
	$triples=NumeroMultiples($signos, 3, $comun);
	if($numtriples==0 && $triples!=0)
		return "No se admiten TRIPLES";
	if($ano<2018 || ($ano==2018 && $jornada<=6))
	{
		if($triples<$numtriples)
			return "Te faltan triples";
		if($triples>$numtriples)
			return "Te sobran triples";
	}
	else if($triples!=0)
		return "No se admiten TRIPLES";
	
	$dobles=NumeroMultiples($signos, 2, $comun);
	if($dobles>$numdobles)
		return "Te sobran dobles";
	if($dobles<$numdobles)
		return "Te faltan dobles";
	
	return "Boleto correcto";
}

function PonCasillas($signo, $nombre, $quecasillas, $ano, $jornada)
{
	if($quecasillas=='ordinaria')
	{
		//Tabla de dobles
		$losdobles=NumeroMultiples($signo, 2, "");
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-200px;margin-left:540px;" id="TablaDoblesErrores">
				<tr><th style="background:gray;color:orange;">Número de dobles ordinaria:</th></tr>
				<tr ><td id="numdobles" align="center" valign="middle">'.$losdobles.'</td></tr></table>';
		echo ($str);

		//Tabla de avisos
		if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin')
			$loserrores=Errores($signo, "", 7, 0, $ano, $jornada);
		else
			$loserrores=Errores($signo, "", 1, 0, $ano, $jornada);
		if($loserrores=="Boleto correcto")
			$elcolor="green";
		else
			$elcolor="red";
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-130px;margin-left:540px;">
				<tr><th align="center" style="background:gray;color:orange">Avisos ordinaria:</th></tr>
				<tr><td id="avisos" align="center" style="color:'.$elcolor.'">'.$loserrores.'</td></tr></table>';
		echo ($str);
	}
	else //caso de loca y comunitaria
	{
		//Tabla de dobles
		if($nombre=='loca')
		{
			$losdobles=NumeroMultiples($signo, 2, "");
			$cartelito="Número de dobles LOCA:";
		}
		else
		{
			$losdobles=NumeroMultiples($signo, 2, "C");
			$cartelito="Número de dobles COMUN:";
		}
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-201px;margin-left:810px;">
				<tr><th align="center" style="background:gray;color:orange">'.$cartelito.'</th></tr>
				<tr ><td id="numdoblesloca" align="center" valign="middle">'.$losdobles.'</td></tr></table>';
		echo $str;
	
		//Tabla de triples si comunitaria
		if($nombre!='loca')
		{
			if($ano<2018 || ($ano==2018 && $jornada<=6))
			{
				$lostriples=NumeroMultiples($signo, 3, "C");
				$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-130px;margin-left:810px;">
					<tr><th align="center" style="background:gray;color:orange">Número de triples COMUN:</th></tr>
					<tr ><td id="numtriplesloca" align="center" valign="middle">'.$lostriples.'</td></tr></table>';
				echo $str;
			}
		}
		
		//Tabla de avisos
		if($nombre=='loca')
		{
			$loserrores=Errores($signo, "", 7, 0, $ano, $jornada);
			$cartelito="Avisos LOCA:";
			$desplazamiento="-130px";
		}
		else
		{
			$loserrores=Errores($signo, "C", 6, 2, $ano, $jornada);
			$cartelito="Avisos COMUN:";
			if($ano<2018 || ($ano==2018 && $jornada<=6))
				$desplazamiento="-59px";
			else
				$desplazamiento="-130px";
		}
		if($loserrores=="Boleto correcto")
			$elcolor="green";
		else
			$elcolor="red";
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:'.$desplazamiento.';margin-left:810px;">
				<tr><th align="center" style="background:gray;color:orange">'.$cartelito.'</th></tr>
				<tr><td align="center" id="avisosloca" style="color:'.$elcolor.'">'.$loserrores.'</td></tr></table>';
		echo $str;
	}
}

function RefrescaCasillas($signo, $nombre, $quecasillas, $ano, $jornada)
{
	if($quecasillas=='ordinariayloca') //Caso ordinaria y loca
	{
		//Tabla de dobles
		$losdobles=NumeroMultiples($signo, 2, "");

		//Tabla de avisos
		if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin' || $nombre=='loca')
			$loserrores=Errores($signo, "", 7, 0, $ano, $jornada);
		else
			$loserrores=Errores($signo, "", 1, 0, $ano, $jornada);
		
		echo $losdobles.'|$|'.$loserrores;
	}
	else //caso comunitaria
	{
		//Tabla de dobles
		$losdobles=NumeroMultiples($signo, 2, "C");
		//Tabla de triples
		$lostriples=NumeroMultiples($signo, 3, "C");
		//Tabla de avisos
		$loserrores=Errores($signo, "C", 6, 2, $ano, $jornada);

		echo $losdobles.'|$|'.$loserrores.'|$|'.$lostriples;
	}
}

/*function Errores($signos, $comun, $numdobles, $numtriples)
{
	if(NumeroMultiples($signos, 0, $comun)==TRUE)
		return "Falta pronóstico en algún partido";
	
	$triples=NumeroMultiples($signos, 3, $comun);
	if($numtriples==0 && $triples!=0)
		return "No se admiten TRIPLES";
	if($triples<$numtriples)
		return "Te faltan triples";
	if($triples>$numtriples)
		return "Te sobran triples";
	
	$dobles=NumeroMultiples($signos, 2, $comun);
	if($dobles>$numdobles)
		return "Te sobran dobles";
	if($dobles<$numdobles)
		return "Te faltan dobles";
	
	return "Boleto correcto";
}

function PonCasillas($signo, $nombre, $quecasillas)
{
	if($quecasillas=='ordinaria')
	{
		//Tabla de dobles
		$losdobles=NumeroMultiples($signo, 2, "");
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-200px;margin-left:540px;" id="TablaDoblesErrores">
				<tr><th style="background:gray;color:orange;">Número de dobles ordinaria:</th></tr>
				<tr ><td id="numdobles" align="center" valign="middle">'.$losdobles.'</td></tr></table>';
		echo ($str);

		//Tabla de avisos
		if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin')
			$loserrores=Errores($signo, "", 7, 0);
		else
			$loserrores=Errores($signo, "", 1, 0);
		if($loserrores=="Boleto correcto")
			$elcolor="green";
		else
			$elcolor="red";
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-130px;margin-left:540px;">
				<tr><th align="center" style="background:gray;color:orange">Avisos ordinaria:</th></tr>
				<tr><td id="avisos" align="center" style="color:'.$elcolor.'">'.$loserrores.'</td></tr></table>';
		echo ($str);
	}
	else //caso de loca y comunitaria
	{
		//Tabla de dobles
		if($nombre=='loca')
		{
			$losdobles=NumeroMultiples($signo, 2, "");
			$cartelito="Número de dobles LOCA:";
		}
		else
		{
			$losdobles=NumeroMultiples($signo, 2, "C");
			$cartelito="Número de dobles COMUN:";
		}
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-201px;margin-left:810px;">
				<tr><th align="center" style="background:gray;color:orange">'.$cartelito.'</th></tr>
				<tr ><td id="numdoblesloca" align="center" valign="middle">'.$losdobles.'</td></tr></table>';
		echo $str;
	
		//Tabla de triples si comunitaria
		if($nombre!='loca')
		{
			$lostriples=NumeroMultiples($signo, 3, "C");
			$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:-130px;margin-left:810px;">
				<tr><th align="center" style="background:gray;color:orange">Número de triples COMUN:</th></tr>
				<tr ><td id="numtriplesloca" align="center" valign="middle">'.$lostriples.'</td></tr></table>';
			echo $str;
		}
		
		//Tabla de avisos
		if($nombre=='loca')
		{
			$loserrores=Errores($signo, "", 7, 0);
			$cartelito="Avisos LOCA:";
			$desplazamiento="-130px";
		}
		else
		{
			$loserrores=Errores($signo, "C", 6, 2);
			$cartelito="Avisos COMUN:";
			$desplazamiento="-59px";
		}
		if($loserrores=="Boleto correcto")
			$elcolor="green";
		else
			$elcolor="red";
		$str = '<table border="1" width="230" style="font-family:arial;position:absolute;margin-top:'.$desplazamiento.';margin-left:810px;">
				<tr><th align="center" style="background:gray;color:orange">'.$cartelito.'</th></tr>
				<tr><td align="center" id="avisosloca" style="color:'.$elcolor.'">'.$loserrores.'</td></tr></table>';
		echo $str;
	}
}

function RefrescaCasillas($signo, $nombre, $quecasillas)
{
	if($quecasillas=='ordinariayloca') //Caso ordinaria y loca
	{
		//Tabla de dobles
		$losdobles=NumeroMultiples($signo, 2, "");

		//Tabla de avisos
		if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin' || $nombre=='loca')
			$loserrores=Errores($signo, "", 7, 0);
		else
			$loserrores=Errores($signo, "", 1, 0);
		
		echo $losdobles.'|$|'.$loserrores;
	}
	else //caso comunitaria
	{
		//Tabla de dobles
		$losdobles=NumeroMultiples($signo, 2, "C");
		//Tabla de triples
		$lostriples=NumeroMultiples($signo, 3, "C");
		//Tabla de avisos
		$loserrores=Errores($signo, "C", 6, 2);

		echo $losdobles.'|$|'.$loserrores.'|$|'.$lostriples;
	}
}*/

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sel=$con->query("SELECT * FROM Jornada");
$jornada= $sel->fetch_assoc();

$sel=$con->query("SELECT * FROM Ano");
$ano= $sel->fetch_assoc();

if($casilla!="PF1C" && $casilla!="PF2C")
{
	if($signo=="false")
		$signo="";
	else
		$signo="checked";
}
/*
$con->query("UPDATE `Pronosticoscom` SET ".$casilla." = '".$signo."' WHERE Jornada = '".$jornada['Jornadaencurso']."'");

//Rellenado de las casillas de número de dobles de número de triples y de errores
$sel = $con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada['Jornadaencurso']);
$signocom=$sel->fetch_assoc();
RefrescaCasillas($signocom, $nombre, "comunitaria", $ano['anoencurso'], $jornada['Jornadaencurso']);
*/

$contador = $con->query("SELECT COUNT(*) FROM Pronosticoscom WHERE Jornada = ".$jornada['Jornadaencurso']);
$resultado = $contador->fetch_array();
if($resultado[0]==0)
{
	$restodediv=$jornada['Jornadaencurso'] % 8;
	switch($restodediv)
	{
		case 1:
			$nombrecom='Julio';
			break;
		case 2:
			$nombrecom='Javier';
			break;
		case 3:
			$nombrecom='FreminG';
			break;
		case 4:
			$nombrecom='Felipe';
			break;
		case 5:
			$nombrecom='Antonio';
			break;
		case 6:
			$nombrecom='Alvaro';
			break;
		case 7:
			$nombrecom='FreminA';
			break;
		case 0:
			$nombrecom='Michel';
			break;
	}
			
	
	$sql = "INSERT INTO Pronosticoscom(Jornada,Usuario) VALUES(".$jornada['Jornadaencurso'].",'".$nombrecom."')";
	$con->query($sql);
}
$con->query("UPDATE `Pronosticoscom` SET ".$casilla." = '".$signo."' WHERE Jornada = '".$jornada['Jornadaencurso']."'");

//Rellenado de las casillas de número de dobles de número de triples y de errores
$sel = $con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada['Jornadaencurso']);
$signocom=$sel->fetch_assoc();
RefrescaCasillas($signocom, $nombre, "comunitaria", $ano['anoencurso'], $jornada['Jornadaencurso']);
?>

