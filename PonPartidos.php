<?php
$nombre=$_GET["q"];

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

/*function RefrescaCasillas($signo, $nombre, $quecasillas, $ano, $jornada)
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
}*/

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sel = $con->query("SELECT * FROM Jornada");
$jornada=$sel->fetch_assoc();

$sel =$con->query("SELECT * FROM Pronosticos WHERE Jornada = ".$jornada['Jornadaencurso']." AND Usuario = '".$nombre."'");
$signo=$sel->fetch_assoc();

$sel =$con->query("SELECT * FROM Pronosticos WHERE Jornada = ".$jornada['Jornadaencurso']." AND Usuario = 'loca'");
$signoloca=$sel->fetch_assoc();

$sel = $con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada['Jornadaencurso']);
$signocom=$sel->fetch_assoc();

$entradas = $con->query("SELECT * FROM Partidos WHERE jornada=".$jornada['Jornadaencurso']);
$fila = $entradas->fetch_assoc();

//Ajustes de textos en función de si toca la loca o comunitaria o ninguna
$testigoloca=FALSE;
$testigocomunitaria=FALSE;
$textocabecera1="";
$textocabecera2="";
$textocabecera3="";
$textofila="";
if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin') //Loca
{
	$restodediv=$jornada['Jornadaencurso'] % 4;
	if(($restodediv==1 && $nombre=='Alfonso') || ($restodediv==2 && $nombre=='Caixero') || ($restodediv==3 && $nombre=='Fermin') || ($restodediv==0 && $nombre=='Manolin'))
	{
		$testigoloca=TRUE;
		$textocabecera1="¡Te ha tocado la LOCA!";
		$textocabecera2='<th style="width:2px;background:orange;"></th><th colspan="3">LOCA</th>';
		$textocabecera3='<th style="width:2px;background:orange;"></th><th>1</th><th>X</th><th>2</th>';			
	}	
}
else //Comunitaria
{
	$restodediv=$jornada['Jornadaencurso'] % 8;
	if(($restodediv==1 && $nombre=='Julio') || ($restodediv==2 && $nombre=='Javier') || ($restodediv==3 && $nombre=='FerminG') || ($restodediv==4 && $nombre=='Felipe')
	   || ($restodediv==5 && $nombre=='Antonio') || ($restodediv==6 && $nombre=='Alvaro') || ($restodediv==7 && $nombre=='FerminA') || ($restodediv==0 && $nombre=='Michel'))
	{
		$testigocomunitaria=TRUE;
		$textocabecera1="¡Te ha tocado la COMUNITARIA!";
		$textocabecera2='<th style="width:3px;background:orange;"></th><th colspan="3">COMUN.</th>';
		$textocabecera3='<th style="width:3px;background:orange;"></th><th>1</th><th>X</th><th>2</th>';
	}
	
/* Así lo hacía antes
	$nombrelower=strtolower($nombre);
	if($signocom['Usuario']==$nombrelower)
	{
		$testigocomunitaria=TRUE;
		$textocabecera1="¡Te ha tocado la COMUNITARIA!";
		$textocabecera2='<th style="width:3px;background:orange;"></th><th colspan="3">COMUN.</th>';
		$textocabecera3='<th style="width:3px;background:orange;"></th><th>1</th><th>X</th><th>2</th>';
	}
*/
}

//La tabla 
	//La Cabecera
$str = '<table style="font-family:arial" border="1"><tr style="background:gray;color:orange;"><th colspan="2">'.$textocabecera1.'</th><th colspan="3">Ordin.</th>'.$textocabecera2.'</tr>
		<tr style="background:gray;color:orange;"><th>Nº</th><th>PARTIDO</th><th>1</th><th>X</th><th>2</th>'.$textocabecera3.'</tr>';
echo ($str);
	//Los partidos y columnas 1X2 de la ordinaria y de la loca/comunitaria (si es que toca)
for($i=1;$i<15;$i++)
{
	switch($i)
	{
		case 10:
			$a='A';
			break;
		case 11:
			$a='B';
			break;
		case 12:
			$a='C';
			break;
		case 13:
			$a='D';
			break;
		case 14:
			$a='E';
			break;
		case 15:
			$a='F';
			break;
		default:
			$a=$i;
			break;
	}
	$str = '<tr><td>'.$i.'</td><td>'.$fila["P".$i].'</td>
			<td><label class="container"><input type="checkbox" id="P'.$a.'1" onclick="PonSigno(checked,id,&#39'.$nombre.'&#39)" '.$signo["P".$a."1"].'><span class="checkmark"></span></label></td>
			<td><label class="container"><input type="checkbox" id="P'.$a.'X" onclick="PonSigno(checked,id,&#39'.$nombre.'&#39)" '.$signo["P".$a."X"].'><span class="checkmark"></span></label></td>
			<td><label class="container"><input type="checkbox" id="P'.$a.'2" onclick="PonSigno(checked,id,&#39'.$nombre.'&#39)" '.$signo["P".$a."2"].'><span class="checkmark"></span></label></td>';
	if($testigoloca==TRUE)
	{
		$str2 = '<td style="width:3px;background:orange;"></td>
				 <td><label class="container"><input type="checkbox" id="P'.$a.'1" onclick="PonSigno(checked,id,&#39loca&#39)" '.$signoloca["P".$a."1"].'><span class="checkmark"></span></label></td>
				 <td><label class="container"><input type="checkbox" id="P'.$a.'X" onclick="PonSigno(checked,id,&#39loca&#39)" '.$signoloca["P".$a."X"].'><span class="checkmark"></span></label></td>
				 <td><label class="container"><input type="checkbox" id="P'.$a.'2" onclick="PonSigno(checked,id,&#39loca&#39)" '.$signoloca["P".$a."2"].'><span class="checkmark"></span></label></td></tr>';
	}
	else if($testigocomunitaria==TRUE)
	{
		$str2 = '<td style="width:3px;background:orange;"></td>
				 <td><label class="container"><input type="checkbox" id="P'.$a.'1C" onclick="PonSignoCom(checked,id,&#39loca&#39)" '.$signocom["P".$a."1C"].'><span class="checkmark"></span></label></td>
				 <td><label class="container"><input type="checkbox" id="P'.$a.'XC" onclick="PonSignoCom(checked,id,&#39loca&#39)" '.$signocom["P".$a."XC"].'><span class="checkmark"></span></label></td>
				 <td><label class="container"><input type="checkbox" id="P'.$a.'2C" onclick="PonSignoCom(checked,id,&#39loca&#39)" '.$signocom["P".$a."2C"].'><span class="checkmark"></span></label></td></tr>';
	}	
	else
		$str2 ='</tr>';
	
	$str=$str.$str2;
	
	//echo utf8_encode($str);
	echo ($str);
}
$str='</table>';
echo $str;
	//El pleno al 15 de la ordinaria
$op10="";
$op11="";
$op12="";
$op1M="";
$op1N="";
$op20="";
$op21="";
$op22="";
$op2M="";
$op2N="";

switch ($signo["PF1"])
{
	case '0':
		$op10="selected";
		break;
	case '1':
		$op11="selected";
		break;
	case '2':
		$op12="selected";
		break;
	case 'M':
		$op1M="selected";
		break;
	default:
		$op1N="selected";
		break;
}

switch ($signo["PF2"])
{
	case '0':
		$op20="selected";
		break;
	case '1':
		$op21="selected";
		break;
	case '2':
		$op22="selected";
		break;
	case 'M':
		$op2M="selected";
		break;
	default:
		$op2N="selected";
		break;
}

echo '<table style="font-family:arial" border="1">';
$str = '<tr style="color:red"><td>P-15</td><td>'.$fila["P15"].'</td>
		<td><select id="PF1" onchange="PonSigno(value,id,&#39'.$nombre.'&#39)"><option id="0_1" value="0" '.$op10.'>0</option><option id="1_1" value="1" '.$op11.'>1</option><option id="2_1" value="2" '.$op12.'>2</option><option id="M_1" value="M" '.$op1M.'>M</option><option id="N_1" value="?" '.$op1N.'>?</option></td>
		<td><select id="PF2" onchange="PonSigno(value,id,&#39'.$nombre.'&#39)"><option id="0_2" value="0" '.$op20.'>0</option><option id="1_2" value="1" '.$op21.'>1</option><option id="2_2" value="2" '.$op22.'>2</option><option id="M_2" value="M" '.$op2M.'>M</option><option id="N_2" value="?" '.$op2N.'>?</option></td>';                                
//echo utf8_encode($str);
echo ($str);
	//Pleno al 15 de la loca/comunitaria
		//La loca
if($testigoloca==TRUE)
{
	$op10="";
	$op11="";
	$op12="";
	$op1M="";
	$op1N="";
	$op20="";
	$op21="";
	$op22="";
	$op2M="";
	$op2N="";
	
	switch ($signoloca["PF1"])
	{
		case '0':
			$op10="selected";
			break;
		case '1':
			$op11="selected";
			break;
		case '2':
			$op12="selected";
			break;
		case 'M':
			$op1M="selected";
			break;
		default:
			$op1N="selected";
			break;
	}
	switch ($signoloca["PF2"])
	{
		case '0':
			$op20="selected";
			break;
		case '1':
			$op21="selected";
			break;
		case '2':
			$op22="selected";
			break;
		case 'M':
			$op2M="selected";
			break;
		default:
			$op2N="selected";
			break;
	}
	$str = '<td style="width:3px;background:orange;"></td>
		    <td><select id="PF1" onchange="PonSigno(value,id,&#39loca&#39)"><option id="0_1" value="0" '.$op10.'>0</option><option id="1_1" value="1" '.$op11.'>1</option><option id="2_1" value="2" '.$op12.'>2</option><option id="M_1" value="M" '.$op1M.'>M</option><option id="N_1" value="?" '.$op1N.'>?</option></td>
		    <td><select id="PF2" onchange="PonSigno(value,id,&#39loca&#39)"><option id="0_2" value="0" '.$op20.'>0</option><option id="1_2" value="1" '.$op21.'>1</option><option id="2_2" value="2" '.$op22.'>2</option><option id="M_2" value="M" '.$op2M.'>M</option><option id="N_2" value="?" '.$op2N.'>?</option></td></tr></table>';
}
		//La comunitaria
else if($testigocomunitaria==TRUE)
{
	$op10="";
	$op11="";
	$op12="";
	$op1M="";
	$op1N="";
	$op20="";
	$op21="";
	$op22="";
	$op2M="";
	$op2N="";
	
	switch ($signocom["PF1C"])
	{
		case '0':
			$op10="selected";
			break;
		case '1':
			$op11="selected";
			break;
		case '2':
			$op12="selected";
			break;
		case 'M':
			$op1M="selected";
			break;
		default:
			$op1N="selected";
			break;
	}
	switch ($signocom["PF2C"])
	{
		case '0':
			$op20="selected";
			break;
		case '1':
			$op21="selected";
			break;
		case '2':
			$op22="selected";
			break;
		case 'M':
			$op2M="selected";
			break;
		default:
			$op2N="selected";
			break;
	}
	$str = '<td style="width:3px;background:orange;"></td>
			<td><select id="PF1C" onchange="PonSignoCom(value,id,&#39'.$nombre.'&#39)"><option id="0_1" value="0" '.$op10.'>0</option><option id="1_1" value="1" '.$op11.'>1</option><option id="2_1" value="2" '.$op12.'>2</option><option id="M_1" value="M" '.$op1M.'>M</option><option id="N_1" value="?" '.$op1N.'>?</option></td>
			<td><select id="PF2C" onchange="PonSignoCom(value,id,&#39'.$nombre.'&#39)"><option id="0_2" value="0" '.$op20.'>0</option><option id="1_2" value="1" '.$op21.'>1</option><option id="2_2" value="2" '.$op22.'>2</option><option id="M_2" value="M" '.$op2M.'>M</option><option id="N_2" value="?" '.$op2N.'>?</option></td></tr></table>';
}
else
	$str = '</tr></table>';

echo ($str);


//Foto del balón
$str = '<img src="Chute.jpg" alt="Quinielas y mus"  height="200" width="180" style="position:absolute;margin-top:-415px;margin-left:565px;"/>';
echo ($str);

//Casillas de dobles y avisos
PonCasillas($signo, $nombre, "ordinaria", 0, 0);

if($testigoloca==TRUE || $testigocomunitaria==TRUE)
{
	$sel = $con->query("SELECT * FROM Ano");
	$ano=$sel->fetch_assoc();
	
	//Foto loca/comunitaria
	$str = '<img src="FotoLoca.jpg" alt="Quinielas y mus" height="200" width="250" style="position:absolute;margin-top:-415px;margin-left:800px;"/>';
	echo ($str);
	
	//Casillas de dobles, triples (solo si comunitaria) y avisos
	if($testigoloca==TRUE) //Caso de loca
		PonCasillas($signoloca, "loca", "locacomun", $ano['anoencurso'], $jornada['Jornadaencurso']);
	else //Caso de comunitaria
		PonCasillas($signocom, $nombre, "locacomun", $ano['anoencurso'], $jornada['Jornadaencurso']);
}
?>
