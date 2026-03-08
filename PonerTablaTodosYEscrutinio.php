<?php
$nombre=$_GET["p"];
$jornada=$_GET["q"];
$todosesc=$_GET["r"];

function DameAciertosyColores ($con, $pronostico, &$ColorCelda, &$sumaciertos, &$sumaciertosspq, $jornada, $nombre)
{
	$sel = $con->query("SELECT * FROM Escrutinio WHERE Jornada = ".$jornada);
	$resultado=$sel->fetch_assoc();
	
	if($nombre=="comun")
		$LadC="C";
	else
		$LadC="";
	
	for($i=0;$i<14;$i++)
	{
		$b=$i+1;
		switch($i)
		{
			case 9:
				$a='A';
				break;
			case 10:
				$a='B';
				break;
			case 11:
				$a='C';
				break;
			case 12:
				$a='D';
				break;
			case 13:
				$a='E';
				break;
			default:
				$a=$i+1;
		}	
		if($resultado["R".$b]=='1' && $pronostico["P".$a."1".$LadC]=='X')
		{
			$ColorCelda[$i][0]='green';
			$aux=$sumaciertos;
			$aux++;
			$sumaciertos=$aux;
		}
		else if($resultado["R".$b]=='X' && $pronostico["P".$a."X".$LadC]=='X')
		{
			$ColorCelda[$i][1]='green';
			$aux=$sumaciertos;
			$aux++;
			$sumaciertos=$aux;
		}
		else if($resultado["R".$b]=='2' && $pronostico["P".$a."2".$LadC]=='X')
		{
			$ColorCelda[$i][2]='green';
			$aux=$sumaciertos;
			$aux++;
			$sumaciertos=$aux;
		}
	}
	$sumaciertosspq=$sumaciertos;
	
	//Pleno al quince
	if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin' || $nombre=='Loca')
	{
		switch($pronostico["PF1"])
		{
			case 'M':
				$golesp1=3;
				break;
			case '2':
				$golesp1=2;
				break;
			case '1':
				$golesp1=1;
				break;
			case '0':
				$golesp1=0;
				break;
			default:
				$golesp1=-1;
				break;
		}
		switch($pronostico["PF2"])
		{
			case 'M':
				$golesp2=3;
				break;
			case '2':
				$golesp2=2;
				break;
			case '1':
				$golesp2=1;
				break;
			case '0':
				$golesp2=0;
				break;
			default:
				$golesp2=-1;
				break;
		}
		if($resultado["R15_1"]=='M')
			$golesr1=3;
		else
			$golesr1=$resultado["R15_1"];
		if($resultado["R15_2"]=='M')
			$golesr2=3;
		else
			$golesr2=$resultado["R15_2"];
		
		if($golesp1!=-1 && $golesp2!=-1 && $golesr1!=-1 && $golesr2!=-1 && (($golesr1>$golesr2 && $golesp1>$golesp2) || ($golesr1==$golesr2 && $golesp1==$golesp2) || ($golesr1<$golesr2 && $golesp1<$golesp2)))
		{
			$aux=$sumaciertos;
			$aux++;
			$sumaciertos=$aux;
			$ColorCelda[14][1]='green';
			if($golesr1==$golesp1 || ($pronostico["PF1"]=='M' && $golesr1>2))
			{
				$aux=$sumaciertos;
				$aux++;
				$sumaciertos=$aux;
				$ColorCelda[14][0]='green';
			}
			if($golesr2==$golesp2 || ($pronostico["PF2"]=='M' && $golesr2>2))
			{
				$aux=$sumaciertos;
				$aux++;
				$sumaciertos=$aux;
				$ColorCelda[14][2]='green';
			}
		}
	}
	else
	{
		if($resultado["R15_1"]>2)
			$golesr1='M';
		else
			$golesr1=$resultado["R15_1"];
		if($resultado["R15_2"]>2)
			$golesr2='M';
		else
			$golesr2=$resultado["R15_2"];
				
		if(($golesr1=='0' || $golesr1=='1' || $golesr1=='2' || $golesr1=='M') && ($golesr2=='0' || $golesr2=='1' || $golesr2=='2' || $golesr2=='M'))
		{
			if(($golesr1==$pronostico["PF1".$LadC]) && ($golesr2==$pronostico["PF2".$LadC]))
			{
				$aux=$sumaciertos;
				$aux++;
				$sumaciertos=$aux;
				$ColorCelda[14][1]='green';
				$ColorCelda[14][0]='green';
				$ColorCelda[14][2]='green';
			}
		}
	}
}

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sel = $con->query("SELECT * FROM Ano");
$ano=$sel->fetch_assoc();

if($jornada==0)
{
	$sel = $con->query("SELECT * FROM Jornada");
	$jornadaencurso=$sel->fetch_assoc();
	$jornada=$jornadaencurso['Jornadaencurso'];
}
$IndiceJornada=$jornada-1; //El indice que se pasará para que se ponga la casilla de jornada. Como los índices empiezan por 0 hay que restar 1 a la jornada.
echo $IndiceJornada.'|$|'; 

//Se inicializan variables de colorines y aciertos
for($i=0;$i<9;$i++)
{
	for($j=0;$j<15;$j++)
	{
		for($k=0;$k<5;$k++)
		{
			$ColorCelda[$i][$j][$k]='white';
		}
	}
}

for($i=0;$i<8;$i++)
{
	$aciertopremio1[$i]=0;
	$aciertopremio2[$i]=0;				
	$trinque[$i]=0;
}

$sumaciertosAl=0;
$sumaciertosCa=0;
$sumaciertosFe=0;
$sumaciertosMa=0;
$sumaciertosLo=0;
$sumaciertosFerA=0;
$sumaciertosMicA=0;
$sumaciertosJulG=0;
$sumaciertosJavM=0;
$sumaciertosFerG=0;
$sumaciertosFelG=0;
$sumaciertosAntL=0;
$sumaciertosAlvL=0;
$sumaciertosComu=0;

$sumaciertosAlspq=0;
$sumaciertosCaspq=0;
$sumaciertosFespq=0;
$sumaciertosMaspq=0;
$sumaciertosLospq=0;
$sumaciertosFerAspq=0;
$sumaciertosMicAspq=0;
$sumaciertosJulGspq=0;
$sumaciertosJavMspq=0;
$sumaciertosFerGspq=0;
$sumaciertosFelGspq=0;
$sumaciertosAntLspq=0;
$sumaciertosAlvLspq=0;
$sumaciertosComuspq=0;

$coloralfonso="orange";
$colorcaixero="orange";
$colorfermin="orange";
$colormanolin="orange";
$colorfermina="orange";
$colormichel="orange";
$colorjulio="orange";
$colorjavier="orange";
$colorferming="orange";
$colorfelipe="orange";
$colorantonio="orange";
$coloralvaro="orange";

//Se cogen los resultados de los partidos
$sel = $con->query("SELECT * FROM Escrutinio WHERE Jornada = ".$jornada);
$resultado=$sel->fetch_assoc();

//Se cogen los partidos
$sel = $con->query("SELECT * FROM Partidos WHERE jornada=".$jornada);
$partido = $sel->fetch_assoc();

//Caso Quiniela Manolin
if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin')
{
	if($todosesc=='todos') //Caso tabla todos
	{
		for($i=0;$i<5;$i++)
		{
			switch($i)
			{
				case 0:
					$usuario='alfonso';
					$sumaciertos=&$sumaciertosAl;
					$sumaciertosspq=&$sumaciertosAlspq;
					break;
				case 1:
					$usuario='caixero';
					$sumaciertos=&$sumaciertosCa;
					$sumaciertosspq=&$sumaciertosCaspq;
					break;
				case 2:
					$usuario='fermin';
					$sumaciertos=&$sumaciertosFe;
					$sumaciertosspq=&$sumaciertosFespq;
					break;
				case 3:
					$usuario='manolin';
					$sumaciertos=&$sumaciertosMa;
					$sumaciertosspq=&$sumaciertosMaspq;
					break;
				case 4:
					$usuario='loca';
					$sumaciertos=&$sumaciertosLo;
					$sumaciertosspq=&$sumaciertosLospq;
					break;
			}
			$sel = $con->query("SELECT * FROM Pronosticos WHERE Jornada = ".$jornada." AND Usuario = '".$usuario."'");
			$pronostico[$i]=$sel->fetch_assoc();
			//Cambio los checked por Xs
			for($j=1;$j<15;$j++)
			{
				switch($j)
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
						$a=$j;
						break;
				}
				if($pronostico[$i]["P".$a."1"]=='checked')
					$pronostico[$i]["P".$a."1"]='X';
				if($pronostico[$i]["P".$a."X"]=='checked')
					$pronostico[$i]["P".$a."X"]='X';
				if($pronostico[$i]["P".$a."2"]=='checked')
					$pronostico[$i]["P".$a."2"]='X';
			}
			DameAciertosyColores ($con, $pronostico[$i], $ColorCelda[$i], $sumaciertos, $sumaciertosspq, $jornada, $nombre);
		}

		$restodediv=$jornada % 4;
		switch ($restodediv)
		{
			case 1:
				$coloralfonso="darkmagenta";
				break;
			case 2:
				$colorcaixero="darkmagenta";
				break;
			case 3:
				$colorfermin="darkmagenta";
				break;
			case 0:
				$colormanolin="darkmagenta";
				break;
		}
		echo '<table style="font-family:arial;" border="2">';
		$str = '<tr style="background:gray;color:orange"><th rowspan="2" style="padding-right:10px;padding-left:10px">Nº</th><th rowspan="2" style="padding-right:150px;padding-left:150px">PARTIDO</th><th rowspan="2" style="padding-right:2px;padding-left:2px">Escrut.</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:2px;color:'.$coloralfonso.'">Alfonso</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:2px;color:'.$colorcaixero.'">Caixero</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:3px;color:'.$colorfermin.'">Fermín</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:2px;color:'.$colormanolin.'">Manolín</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:10px;padding-left:10px">LOCA</th></tr>';
		echo ($str);
		$str = '<tr style="background:gray;color:orange;font-weight:bold;"><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td></tr>';
		echo ($str);

		for($i=0;$i<15;$i++)
		{
			switch($i)
			{
				case 9:
					$a='A';
					break;
				case 10:
					$a='B';
					break;
				case 11:
					$a='C';
					break;
				case 12:
					$a='D';
					break;
				case 13:
					$a='E';
					break;
				case 14:
					$a='F';
					break;
				default:
					$a=$i+1;
					break;
			}
			$b=$i+1;
			if($i<14)
			{
				$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$b.'</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P".$b].'</td><td>'.$resultado["R".$b].'</td>
						<td style="background:'.$ColorCelda[0][$i][0].'">'.$pronostico[0]["P".$a."1"].'</td><td style="background:'.$ColorCelda[0][$i][1].'">'.$pronostico[0]["P".$a."X"].'</td><td style="background:'.$ColorCelda[0][$i][2].'">'.$pronostico[0]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[1][$i][0].'">'.$pronostico[1]["P".$a."1"].'</td><td style="background:'.$ColorCelda[1][$i][1].'">'.$pronostico[1]["P".$a."X"].'</td><td style="background:'.$ColorCelda[1][$i][2].'">'.$pronostico[1]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[2][$i][0].'">'.$pronostico[2]["P".$a."1"].'</td><td style="background:'.$ColorCelda[2][$i][1].'">'.$pronostico[2]["P".$a."X"].'</td><td style="background:'.$ColorCelda[2][$i][2].'">'.$pronostico[2]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[3][$i][0].'">'.$pronostico[3]["P".$a."1"].'</td><td style="background:'.$ColorCelda[3][$i][1].'">'.$pronostico[3]["P".$a."X"].'</td><td style="background:'.$ColorCelda[3][$i][2].'">'.$pronostico[3]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[4][$i][0].'">'.$pronostico[4]["P".$a."1"].'</td><td style="background:'.$ColorCelda[4][$i][1].'">'.$pronostico[4]["P".$a."X"].'</td><td style="background:'.$ColorCelda[4][$i][2].'">'.$pronostico[4]["P".$a."2"].'</td></tr>';                                        
			}
			else
			{
				$str = '<tr style="color:red"><td style="background:gray;font-weight:bold">P-15</td><td style="background:gray;color:red;font-weight:bold">'.$partido["P15"].'</td><td>'.$resultado["R15_1"]."-".$resultado["R15_2"].'</td>
						<td style="background:'.$ColorCelda[0][14][0].'">'.$pronostico[0]["PF1"].'</td><td style="background:'.$ColorCelda[0][14][1].'">-</td><td style="background:'.$ColorCelda[0][14][2].'">'.$pronostico[0]["PF2"].'</td>
						<td style="background:'.$ColorCelda[1][14][0].'">'.$pronostico[1]["PF1"].'</td><td style="background:'.$ColorCelda[1][14][1].'">-</td><td style="background:'.$ColorCelda[1][14][2].'">'.$pronostico[1]["PF2"].'</td>
						<td style="background:'.$ColorCelda[2][14][0].'">'.$pronostico[2]["PF1"].'</td><td style="background:'.$ColorCelda[2][14][1].'">-</td><td style="background:'.$ColorCelda[2][14][2].'">'.$pronostico[2]["PF2"].'</td>
						<td style="background:'.$ColorCelda[3][14][0].'">'.$pronostico[3]["PF1"].'</td><td style="background:'.$ColorCelda[3][14][1].'">-</td><td style="background:'.$ColorCelda[3][14][2].'">'.$pronostico[3]["PF2"].'</td>
						<td style="background:'.$ColorCelda[4][14][0].'">'.$pronostico[4]["PF1"].'</td><td style="background:'.$ColorCelda[4][14][1].'">-</td><td style="background:'.$ColorCelda[4][14][2].'">'.$pronostico[4]["PF2"].'</td></tr>';
			}
			//echo utf8_encode($str);
			echo ($str);
		}
		$str = '<tr style="color:green;font-weight:bold"><td colspan="3" align="right" style="background:gray">SUMA DE ACIERTOS LOT y APU. del Estado:</td>
				<td colspan="3">'.$sumaciertosAlspq.'</td>
				<td colspan="3">'.$sumaciertosCaspq.'</td>
				<td colspan="3">'.$sumaciertosFespq.'</td>
				<td colspan="3">'.$sumaciertosMaspq.'</td>
				<td colspan="3">'.$sumaciertosLospq.'</td></tr>';
		echo ($str); 
		$str = '<tr style="color:darkmagenta;font-weight:bold"><td colspan="3" align="right" style="background:gray">SUMA DE ACIERTOS WEB:</td>
				<td colspan="3">'.$sumaciertosAl.'</td>
				<td colspan="3">'.$sumaciertosCa.'</td>
				<td colspan="3">'.$sumaciertosFe.'</td>
				<td colspan="3">'.$sumaciertosMa.'</td>
				<td colspan="3">'.$sumaciertosLo.'</td></tr></table>';
		echo ($str);
	}
	else //Caso tabla escrutinio
	{
		$Singosdesdobles=array(array(0,0,1,1,1,1,0,0,1,1,0,0,1,1,0,0),
							   array(0,0,1,1,1,1,0,0,0,0,1,1,0,0,1,1),
							   array(0,0,1,1,0,0,1,1,1,1,0,0,0,0,1,1),
							   array(0,0,1,1,0,0,1,1,0,0,1,1,1,1,0,0),
							   array(0,1,0,1,1,0,1,0,0,1,0,1,0,1,0,1),
							   array(0,1,0,1,0,1,0,1,1,0,1,0,0,1,0,1),
							   array(0,1,0,1,0,1,0,1,0,1,0,1,1,0,1,0));

		for($i=0;$i<5;$i++)
		{
			switch($i)
			{
				case 0:
					$usuario='alfonso';
					$usuariocartelito='Alfonso Martínez Sánchez-Sicilia';
					$sumaciertos=&$sumaciertosAl;
					$sumaciertosspq=&$sumaciertosAlspq;
					break;
				case 1:
					$usuario='caixero';
					$usuariocartelito='Fernando Sánchez Rodríguez';
					$sumaciertos=&$sumaciertosCa;
					$sumaciertosspq=&$sumaciertosCaspq;
					break;
				case 2:
					$usuario='fermin';
					$usuariocartelito='Fermín García-Mina Cabredo';
					$sumaciertos=&$sumaciertosFe;
					$sumaciertosspq=&$sumaciertosFespq;
					break;
				case 3:
					$usuario='manolin';
					$usuariocartelito='Manuel Ibáñez Macías';
					$sumaciertos=&$sumaciertosMa;
					$sumaciertosspq=&$sumaciertosMaspq;
					break;
				case 4:
					$usuario='loca';
					$usuariocartelito='LA LOCA';
					$sumaciertos=&$sumaciertosLo;
					$sumaciertosspq=&$sumaciertosLospq;
					break;
			}
			$sel = $con->query("SELECT * FROM Pronosticos WHERE Jornada = ".$jornada." AND Usuario = '".$usuario."'");
			$pronostico=$sel->fetch_assoc();
			
			//Cambio de checkeds por exises
			for($j=1;$j<15;$j++)
			{
				switch($j)
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
					default:
						$a=$j;
						break;
				}
				if($pronostico["P".$a."1"]=='checked')
					$pronostico["P".$a."1"]='X';
				if($pronostico["P".$a."X"]=='checked')
					$pronostico["P".$a."X"]='X';
				if($pronostico["P".$a."2"]=='checked')
					$pronostico["P".$a."2"]='X';
			}
			
			$str = '<table style="font-family:arial" border="1">
					<tr style="background:gray;color:orange;"><th colspan="25">'.$usuariocartelito.'</th></tr>
					<tr style="background:gray;color:orange;"><th rowspan="2" style="padding-right:10px;padding-left:10px">Nº</th>
					<th rowspan="2" style="padding-right:150px;padding-left:150px">PARTIDO</th><th rowspan="18" style="background:gray;padding-left:2px"></th>
					<th rowspan="2">Escr.</th><th rowspan="17" style="background:gray;padding-left:2px"></th>
					<th colspan="3">Pronos.</th><th rowspan="18" style="background:gray;padding-left:2px"></th>
					<th colspan="16">Desarrollo</th></tr>
					<tr style="background:gray;color:orange;"><th>1</th><th>X</th><th>2</th>
					<th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th><th>13</th><th>14</th><th>15</th><th>16</th></tr>';
			echo $str;
			
			//Partidos normales
			$numerodedoble=0;
			$aciertos=0;
			for($j=0;$j<16;$j++)
				$aciertosdesarrollo[$j]=0;
			for($j=1;$j<15;$j++)
			{
				switch ($j)
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
					default:
						$a=$j;
						break;
				}
				$coloracierto[0]='white';
				$coloracierto[1]='white';
				$coloracierto[2]='white';
				if($resultado["R".$j]=='1' && $pronostico["P".$a."1"]=='X')
				{
					$coloracierto[0]='green';
					$aciertos++;
				}
				if($resultado["R".$j]=='X' && $pronostico["P".$a."X"]=='X')
				{
					$coloracierto[1]='green';
					$aciertos++;
				}
				if($resultado["R".$j]=='2' && $pronostico["P".$a."2"]=='X')
				{
					$coloracierto[2]='green';
					$aciertos++;
				}
				
				$casillasmarcadas=0;
				$marcadosencillo='0';
				$marcadodoble[0]='0';
				$marcadodoble[1]='0';
				if($pronostico["P".$a."1"]=='X')
				{
					$casillasmarcadas++;
					$marcadosencillo='1';
					$marcadodoble[0]='1';
				}
				if($pronostico["P".$a."X"]=='X')
				{
					$casillasmarcadas++;
					$marcadosencillo='X';
					if($marcadodoble[0]=='1')
						$marcadodoble[1]='X';
					else
						$marcadodoble[0]='X';
				}
				if($pronostico["P".$a."2"]=='X')
				{
					$casillasmarcadas++;
					$marcadosencillo='2';
					$marcadodoble[1]='2';
				}
				if($casillasmarcadas==2)
				{
					for($k=0;$k<16;$k++)
					{
						$ColorCeldaDesarrollo[$k]='white';
						$signodesarrollo[$k]=$marcadodoble[$Singosdesdobles[$numerodedoble][$k]];
						if($resultado["R".$j]==$signodesarrollo[$k])
						{
							$ColorCeldaDesarrollo[$k]='green';
							$aciertosdesarrollo[$k]++;
						}
					}
					$numerodedoble++;
				}
				else
				{
					for($k=0;$k<16;$k++)
					{
						$signodesarrollo[$k]=$marcadosencillo;
						$ColorCeldaDesarrollo[$k]='white';
					}
					if($resultado["R".$j]==$marcadosencillo)
					{
						for($k=0;$k<16;$k++)
						{
							$ColorCeldaDesarrollo[$k]='green';
							$aciertosdesarrollo[$k]++;
						}
					}
				}

				$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$j.'</td>
						<td style="background:gray;color:orange;font-weight:bold">'.$partido["P".$j].'</td>
						<td style="color:gray">'.$resultado["R".$j].'</td>
						<td style="background:'.$coloracierto[0].'">'.$pronostico["P".$a."1"].'</td>
						<td style="background:'.$coloracierto[1].'">'.$pronostico["P".$a."X"].'</td>
						<td style="background:'.$coloracierto[2].'">'.$pronostico["P".$a."2"].'</td>';
				//echo utf8_encode($str);
				echo ($str);
				for($k=0;$k<16;$k++)
					echo '<td style="background:'.$ColorCeldaDesarrollo[$k].'">'.$signodesarrollo[$k].'</td>';
				echo '</tr>';
			}
			
			//Pleno al 15
			$coloraciertop15[0]='white';
			$coloraciertop15[1]='white';
			$coloraciertop15[2]='white';
			switch($pronostico["PF1"])
			{
				case 'M':
					$golesp1=3;
					break;
				case '2':
					$golesp1=2;
					break;
				case '1':
					$golesp1=1;
					break;
				case '0':
					$golesp1=0;
					break;
				default:
					$golesp1=-1;
					break;
			}
			switch($pronostico["PF2"])
			{
				case 'M':
					$golesp2=3;
					break;
				case '2':
					$golesp2=2;
					break;
				case '1':
					$golesp2=1;
					break;
				case '0':
					$golesp2=0;
					break;
				default:
					$golesp2=-1;
					break;
			}
			if($resultado["R15_1"]=='M')
				$golesr1=3;
			else
				$golesr1=$resultado["R15_1"];
			if($resultado["R15_2"]=='M')
				$golesr2=3;
			else
				$golesr2=$resultado["R15_2"];
			
			if(($golesr1<$golesr2 && $golesp1<$golesp2) || ($golesr1==$golesr2 && $golesp1==$golesp2) || ($golesr1>$golesr2 && $golesp1>$golesp2)) 
			{
				$aciertos++;
				$coloraciertop15[1]='green';
				
				if($resultado["R15_1"]==$pronostico["PF1"] || ($pronostico["PF1"]=='M' && $golesr1>2))
				{
					$aciertos++;
					$coloraciertop15[0]='green';
				}
				if($resultado["R15_2"]==$pronostico["PF2"] || ($pronostico["PF2"]=='M' && $golesr2>2))
				{
					$aciertos++;
					$coloraciertop15[2]='green';
				}
			}
			$plenoalquince=$resultado["R15_1"].'-'.$resultado["R15_2"];
			$premio=0;
			for($j=0;$j<16;$j++)
			{
				if($aciertosdesarrollo[$j]==14)
				{
					$premio+=$resultado["P2"];
					if($golesr1==$golesp1 && $golesr2==$golesp2)
					{
						$plenoalquince="¡¡¡ HAS TRINCAO UN PLENO AL QUINCE MAJETE !!!";
						$premio+=$resultado["P1"];
					}
				}
				else if($aciertosdesarrollo[$j]==13)
					$premio+=$resultado["P3"];
				else if($aciertosdesarrollo[$j]==12)
					$premio+=$resultado["P4"];
				else if($aciertosdesarrollo[$j]==11)
					$premio+=$resultado["P5"];
				else if($aciertosdesarrollo[$j]==10)
					$premio+=$resultado["P6"];
			}	

			$str = '<tr><td style="background:gray;color:red;font-weight:bold">P-15</td>
					<td style="background:gray;color:red;font-weight:bold">'.$partido["P15"].'</td>
					<td style="color:red">'.$resultado["R15_1"]."-".$resultado["R15_2"].'</td>
					<td style="color:red;background:'.$coloraciertop15[0].'">'.$pronostico["PF1"].'</td>
					<td style="color:red;background:'.$coloraciertop15[1].'">-</td>
					<td style="color:red;background:'.$coloraciertop15[2].'">'.$pronostico["PF2"].'</td>
					<td colspan="16" style="color:red">'.$plenoalquince.'</td></tr>';
			echo ($str);
			//echo utf8_encode($str);
			
			//Fila aciertos
			$str = '<tr><td style="background:gray;color:green;font-weight:bold" colspan="2">ACIERTOS:</td><td style="color:green;font-weight:bold" colspan="5">'.$aciertos.'</td>';
			echo ($str);
			for($j=0;$j<16;$j++)
				echo '<td style="color:green;font-weight:bold" >'.$aciertosdesarrollo[$j].'</td>';
			echo '</tr>';
			
			//Fila premios
			$premio=number_format ( $premio , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
			$str = '<tr><td style="background:gray;color:blue;font-weight:bold" colspan="2">PREMIO:</td>
					<td style="color:blue;font-weight:bold" colspan="23">'.$premio.'.-Euros</td></tr>';
			echo $str;
			echo '</table><br />';
		}
	}
}
//Caso QuinielaMina
else
{
	if($resultado["R15_1"]>2)
		$golesr1='M';
	else
		$golesr1=$resultado["R15_1"];
	if($resultado["R15_2"]>2)
		$golesr2='M';
	else
		$golesr2=$resultado["R15_2"];
	
	//Mezclado tabla todos y tabla escrutinio
	$olvidadizo=array(TRUE,TRUE,TRUE,TRUE,TRUE,TRUE,TRUE,TRUE);
	$menoraciertos=100;
	for($i=0;$i<9;$i++)
	{
		//Se resetean las variables de desarrollo de las apuestas
		$desarrollo[$i][0]="";
		$desarrollo[$i][1]="";

		//Se continua con lo demás
		switch($i)
		{
			case 0:
				$usuario='fermina';
				$sumaciertos=&$sumaciertosFerA;
				$sumaciertosspq=&$sumaciertosFerAspq;
				break;
			case 1:
				$usuario='michel';
				$sumaciertos=&$sumaciertosMicA;
				$sumaciertosspq=&$sumaciertosMicAspq;
				break;
			case 2:
				$usuario='julio';
				$sumaciertos=&$sumaciertosJulG;
				$sumaciertosspq=&$sumaciertosJulGspq;
				break;
			case 3:
				$usuario='javier';
				$sumaciertos=&$sumaciertosJavM;
				$sumaciertosspq=&$sumaciertosJavMspq;
				break;
			case 4:
				$usuario='ferming';
				$sumaciertos=&$sumaciertosFerG;
				$sumaciertosspq=&$sumaciertosFerGspq;
				break;
			case 5:
				$usuario='felipe';
				$sumaciertos=&$sumaciertosFelG;
				$sumaciertosspq=&$sumaciertosFelGspq;
				break;
			case 6:
				$usuario='antonio';
				$sumaciertos=&$sumaciertosAntL;
				$sumaciertosspq=&$sumaciertosAntLspq;
				break;
			case 7:
				$usuario='alvaro';
				$sumaciertos=&$sumaciertosAlvL;
				$sumaciertosspq=&$sumaciertosAlvLspq;
				break;
			case 8:
				$usuario='comun';
				$sumaciertos=&$sumaciertosComu;
				$sumaciertosspq=&$sumaciertosComuspq;
				break;
		}
		if($usuario=='comun')
		{
			$sel = $con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada);
			$pronostico[$i]=$sel->fetch_assoc();
			$nombre='comun';
			$LaC="C";
		}
		else
		{
			$sel = $con->query("SELECT * FROM Pronosticos WHERE Jornada = ".$jornada." AND Usuario = '".$usuario."'");
			$pronostico[$i]=$sel->fetch_assoc();
			$LaC="";
		}
		//Cambio los checked por Xs
		for($j=1;$j<15;$j++)
		{
			switch($j)
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
					$a=$j;
					break;
			}
			if($pronostico[$i]["P".$a."1".$LaC]=='checked')
			{
				$pronostico[$i]["P".$a."1".$LaC]='X';
				if($usuario!='comun')
					$olvidadizo[$i]=FALSE;
			}
			if($pronostico[$i]["P".$a."X".$LaC]=='checked')
			{
				$pronostico[$i]["P".$a."X".$LaC]='X';
				if($usuario!='comun')
					$olvidadizo[$i]=FALSE;
			}
			if($pronostico[$i]["P".$a."2".$LaC]=='checked')
			{
				$pronostico[$i]["P".$a."2".$LaC]='X';
				if($usuario!='comun')
					$olvidadizo[$i]=FALSE;
			}
		}
		DameAciertosyColores ($con, $pronostico[$i], $ColorCelda[$i], $sumaciertos, $sumaciertosspq, $jornada, $nombre);
		if($usuario!='comun' && $olvidadizo[$i]==FALSE && $sumaciertos<$menoraciertos)
			$menoraciertos=$sumaciertos;
	}
	
	//Puntuación de olvidadizos
	if($menoraciertos!=100)
	{
		for($i=0;$i<8;$i++)
		{
			switch($i)
			{
				case 0:
					$sumaciertos=&$sumaciertosFerA;
					break;
				case 1:
					$sumaciertos=&$sumaciertosMicA;
					break;
				case 2:
					$sumaciertos=&$sumaciertosJulG;
					break;
				case 3:
					$sumaciertos=&$sumaciertosJavM;
					break;
				case 4:
					$sumaciertos=&$sumaciertosFerG;
					break;
				case 5:
					$sumaciertos=&$sumaciertosFelG;
					break;
				case 6:
					$sumaciertos=&$sumaciertosAntL;
					break;
				case 7:
					$sumaciertos=&$sumaciertosAlvL;
					break;
			}
			if($olvidadizo[$i]==TRUE)
			{
				if($menoraciertos>0)
					$sumaciertos=$menoraciertos-1;
				else
					$sumaciertos=0;
			}
		}
	}			
	
	/*$sel = $con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada);
	$signocom=$sel->fetch_assoc();
	$nombrelower=strtolower($signocom['Usuario']);
	switch ($nombrelower)
	{
		case 'fermina':
			$colorfermina="darkmagenta";
			break;
		case 'michel':
			$colormichel="darkmagenta";
			break;
		case 'julio':
			$colorjulio="darkmagenta";
			break;
		case 'javier':
			$colorjavier="darkmagenta";
			break;
		case 'ferming':
			$colorferming="darkmagenta";
			break;
		case 'felipe':
			$colorfelipe="darkmagenta";
			break;
		case 'antonio':
			$colorantonio="darkmagenta";
			break;
		case 'alvaro':
			$coloralvaro="darkmagenta";
			break;
	}*/
	$restodediv=$jornada % 8;
	switch ($restodediv)
	{
		case 7:
			$colorfermina="darkmagenta";
			break;
		case 0:
			$colormichel="darkmagenta";
			break;
		case 1:
			$colorjulio="darkmagenta";
			break;
		case 2:
			$colorjavier="darkmagenta";
			break;
		case 3:
			$colorferming="darkmagenta";
			break;
		case 4:
			$colorfelipe="darkmagenta";
			break;
		case 5:
			$colorantonio="darkmagenta";
			break;
		case 6:
			$coloralvaro="darkmagenta";
			break;
	}
	if($todosesc=='todos')
	{
		echo '<table style="font-family:arial;" border="2">';
		$str = '<tr style="background:gray;color:orange"><th rowspan="2" style="padding-right:10px;padding-left:10px">Nº</th><th rowspan="2" style="padding-right:150px;padding-left:150px">PARTIDO</th><th rowspan="2" style="padding-right:2px;padding-left:2px">Escrut.</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:2px;color:'.$colorfermina.'">FermínA</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:7px;padding-left:7px;color:'.$colormichel.'">Michel</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:15px;padding-left:15px;color:'.$colorjulio.'">Julio</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:9px;padding-left:9px;color:'.$colorjavier.'">Javier</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:2px;color:'.$colorferming.'">FermínG</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:9px;padding-left:9px;color:'.$colorfelipe.'">Felipe</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:2px;padding-left:2px;color:'.$colorantonio.'">Antonio</th><th rowspan="19" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:7px;padding-left:7px;color:'.$coloralvaro.'">Álvaro</th><th rowspan="19" style="background:gray;padding-left:10px"></th>
				<th colspan="3" style="padding-right:3px;padding-left:3px;color:orange">Común</th></th></tr>';
		echo ($str);
		$str = '<tr style="background:gray;color:orange;font-weight:bold;"><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td><td>1</td><td>X</td><td>2</td></tr>';
		echo ($str);
	}
	else
	{
		echo '<table style="font-family:arial" border="1">';
		$str = '<tr style="background:gray;color:orange;"><th rowspan="3" style="padding-right:10px;padding-left:10px">Nº</th><th rowspan="3" style="padding-right:150px;padding-left:150px">PARTIDO</th><th rowspan="3">Escr.</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colorfermina.'">Fermín A.</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colormichel.'">Michel</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colorjulio.'">Julio</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colorjavier.'">Javier</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colorferming.'">Fermín G.</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colorfelipe.'">Felipe</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$colorantonio.'">Antonio</th><th rowspan="20" style="background:gray;padding-left:2px"></th>
				<th colspan="6" style="color:'.$coloralvaro.'">Alvaro</th></tr>';
		echo $str;
		$str = '<tr style="background:gray;color:orange;">
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th>
				<th colspan="3">Pronos.</th><th rowspan="16" style="background:gray;padding-left:2px"></th><th colspan="2">Des.</th></tr>';
		echo $str;
		$str = '<tr style="background:gray;color:orange;">
				<th>1</th><th>X</th><th>2</th><th>1</th><th>2</th><th>1</th><th>X</th><th>2</th><th>1</th><th>2</th>
				<th>1</th><th>X</th><th>2</th><th>1</th><th>2</th><th>1</th><th>X</th><th>2</th><th>1</th><th>2</th>
				<th>1</th><th>X</th><th>2</th><th>1</th><th>2</th><th>1</th><th>X</th><th>2</th><th>1</th><th>2</th>
				<th>1</th><th>X</th><th>2</th><th>1</th><th>2</th><th>1</th><th>X</th><th>2</th><th>1</th><th>2</th></tr>';
		echo $str;
	}

	for($i=0;$i<15;$i++)
	{
		switch($i)
		{
			case 9:
				$a='A';
				break;
			case 10:
				$a='B';
				break;
			case 11:
				$a='C';
				break;
			case 12:
				$a='D';
				break;
			case 13:
				$a='E';
				break;
			case 14:
				$a='F';
				break;
			default:
				$a=$i+1;
				break;
		}
		$b=$i+1;
		if($todosesc=='todos')
		{
			if($i<14)
			{
				$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$b.'</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P".$b].'</td><td style="color:gray">'.$resultado["R".$b].'</td>
						<td style="background:'.$ColorCelda[0][$i][0].'">'.$pronostico[0]["P".$a."1"].'</td><td style="background:'.$ColorCelda[0][$i][1].'">'.$pronostico[0]["P".$a."X"].'</td><td style="background:'.$ColorCelda[0][$i][2].'">'.$pronostico[0]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[1][$i][0].'">'.$pronostico[1]["P".$a."1"].'</td><td style="background:'.$ColorCelda[1][$i][1].'">'.$pronostico[1]["P".$a."X"].'</td><td style="background:'.$ColorCelda[1][$i][2].'">'.$pronostico[1]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[2][$i][0].'">'.$pronostico[2]["P".$a."1"].'</td><td style="background:'.$ColorCelda[2][$i][1].'">'.$pronostico[2]["P".$a."X"].'</td><td style="background:'.$ColorCelda[2][$i][2].'">'.$pronostico[2]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[3][$i][0].'">'.$pronostico[3]["P".$a."1"].'</td><td style="background:'.$ColorCelda[3][$i][1].'">'.$pronostico[3]["P".$a."X"].'</td><td style="background:'.$ColorCelda[3][$i][2].'">'.$pronostico[3]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[4][$i][0].'">'.$pronostico[4]["P".$a."1"].'</td><td style="background:'.$ColorCelda[4][$i][1].'">'.$pronostico[4]["P".$a."X"].'</td><td style="background:'.$ColorCelda[4][$i][2].'">'.$pronostico[4]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[5][$i][0].'">'.$pronostico[5]["P".$a."1"].'</td><td style="background:'.$ColorCelda[5][$i][1].'">'.$pronostico[5]["P".$a."X"].'</td><td style="background:'.$ColorCelda[5][$i][2].'">'.$pronostico[5]["P".$a."2"].'</td>                                        
						<td style="background:'.$ColorCelda[6][$i][0].'">'.$pronostico[6]["P".$a."1"].'</td><td style="background:'.$ColorCelda[6][$i][1].'">'.$pronostico[6]["P".$a."X"].'</td><td style="background:'.$ColorCelda[6][$i][2].'">'.$pronostico[6]["P".$a."2"].'</td>                                        
						<td style="background:'.$ColorCelda[7][$i][0].'">'.$pronostico[7]["P".$a."1"].'</td><td style="background:'.$ColorCelda[7][$i][1].'">'.$pronostico[7]["P".$a."X"].'</td><td style="background:'.$ColorCelda[7][$i][2].'">'.$pronostico[7]["P".$a."2"].'</td>
						<td style="background:'.$ColorCelda[8][$i][0].'">'.$pronostico[8]["P".$a."1C"].'</td><td style="background:'.$ColorCelda[8][$i][1].'">'.$pronostico[8]["P".$a."XC"].'</td><td style="background:'.$ColorCelda[8][$i][2].'">'.$pronostico[8]["P".$a."2C"].'</td></tr>';
			}
			else
			{
				$str = '<tr style="color:red"><td style="background:gray;font-weight:bold">P-15</td><td style="background:gray;color:red;font-weight:bold">'.$partido["P15"].'</td><td>'.$golesr1."-".$golesr2.'</td>
						<td style="background:'.$ColorCelda[0][14][0].'">'.$pronostico[0]["PF1"].'</td><td style="background:'.$ColorCelda[0][14][1].'">-</td><td style="background:'.$ColorCelda[0][14][2].'">'.$pronostico[0]["PF2"].'</td>
						<td style="background:'.$ColorCelda[1][14][0].'">'.$pronostico[1]["PF1"].'</td><td style="background:'.$ColorCelda[1][14][1].'">-</td><td style="background:'.$ColorCelda[1][14][2].'">'.$pronostico[1]["PF2"].'</td>
						<td style="background:'.$ColorCelda[2][14][0].'">'.$pronostico[2]["PF1"].'</td><td style="background:'.$ColorCelda[2][14][1].'">-</td><td style="background:'.$ColorCelda[2][14][2].'">'.$pronostico[2]["PF2"].'</td>
						<td style="background:'.$ColorCelda[3][14][0].'">'.$pronostico[3]["PF1"].'</td><td style="background:'.$ColorCelda[3][14][1].'">-</td><td style="background:'.$ColorCelda[3][14][2].'">'.$pronostico[3]["PF2"].'</td>
						<td style="background:'.$ColorCelda[4][14][0].'">'.$pronostico[4]["PF1"].'</td><td style="background:'.$ColorCelda[4][14][1].'">-</td><td style="background:'.$ColorCelda[4][14][2].'">'.$pronostico[4]["PF2"].'</td>
						<td style="background:'.$ColorCelda[5][14][0].'">'.$pronostico[5]["PF1"].'</td><td style="background:'.$ColorCelda[5][14][1].'">-</td><td style="background:'.$ColorCelda[5][14][2].'">'.$pronostico[5]["PF2"].'</td>
						<td style="background:'.$ColorCelda[6][14][0].'">'.$pronostico[6]["PF1"].'</td><td style="background:'.$ColorCelda[6][14][1].'">-</td><td style="background:'.$ColorCelda[6][14][2].'">'.$pronostico[6]["PF2"].'</td>
						<td style="background:'.$ColorCelda[7][14][0].'">'.$pronostico[7]["PF1"].'</td><td style="background:'.$ColorCelda[7][14][1].'">-</td><td style="background:'.$ColorCelda[7][14][2].'">'.$pronostico[7]["PF2"].'</td>
						<td style="background:'.$ColorCelda[8][14][0].'">'.$pronostico[8]["PF1C"].'</td><td style="background:'.$ColorCelda[8][14][1].'">-</td><td style="background:'.$ColorCelda[8][14][2].'">'.$pronostico[8]["PF2C"].'</td></tr>';
			}
			//echo utf8_encode($str);
			echo ($str);
		}
		else
		{
			if($i<14)
			{
				for($j=0;$j<8;$j++)
				{
					if($pronostico[$j]["P".$a."1"]=='X')
					{
						$desarrollo[$j][0]='1';
						if($resultado["R".$b]=='1')
						{
							$ColorCelda[$j][$i][3]='green';
							$aciertopremio1[$j]++;
						}
						if($pronostico[$j]["P".$a."X"]=='X')
						{
							$desarrollo[$j][1]='X';
							if($resultado["R".$b]=='X')
							{
								$ColorCelda[$j][$i][4]='green';
								$aciertopremio2[$j]++;
							}
						}
						else if($pronostico[$j]["P".$a."2"]=='X')
						{
							$desarrollo[$j][1]='2';
							if($resultado["R".$b]=='2')
							{
								$ColorCelda[$j][$i][4]='green';
								$aciertopremio2[$j]++;
							}
						}
						else
						{
							$desarrollo[$j][1]='1';
							if($resultado["R".$b]=='1')
							{
								$ColorCelda[$j][$i][4]='green';
								$aciertopremio2[$j]++;
							}
						}
					}
					else if($pronostico[$j]["P".$a."X"]=='X')
					{
						$desarrollo[$j][0]='X';
						if($resultado["R".$b]=='X')
						{
							$ColorCelda[$j][$i][3]='green';
							$aciertopremio1[$j]++;
						}
						if($pronostico[$j]["P".$a."2"]=='X')
						{
							$desarrollo[$j][1]='2';
							if($resultado["R".$b]=='2')
							{
								$ColorCelda[$j][$i][4]='green';
								$aciertopremio2[$j]++;
							}
						}
						else
						{
							$desarrollo[$j][1]='X';
							if($resultado["R".$b]=='X')
							{
								$ColorCelda[$j][$i][4]='green';
								$aciertopremio2[$j]++;
							}
						}
					}
					else
					{
						$desarrollo[$j][0]='2';
						$desarrollo[$j][1]='2';
						if($resultado["R".$b]=='2')
						{
							$ColorCelda[$j][$i][3]='green';
							$ColorCelda[$j][$i][4]='green';
							$aciertopremio1[$j]++;
							$aciertopremio2[$j]++;
						}
					}
				}
				
				$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$b.'</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P".$b].'</td><td style="color:gray">'.$resultado["R".$b].'
						<td style="background:'.$ColorCelda[0][$i][0].'">'.$pronostico[0]["P".$a."1"].'</td><td style="background:'.$ColorCelda[0][$i][1].'">'.$pronostico[0]["P".$a."X"].'</td><td style="background:'.$ColorCelda[0][$i][2].'">'.$pronostico[0]["P".$a."2"].'<td style="background:'.$ColorCelda[0][$i][3].'">'.$desarrollo[0][0].'</td><td style="background:'.$ColorCelda[0][$i][4].'">'.$desarrollo[0][1].'</td>
						<td style="background:'.$ColorCelda[1][$i][0].'">'.$pronostico[1]["P".$a."1"].'</td><td style="background:'.$ColorCelda[1][$i][1].'">'.$pronostico[1]["P".$a."X"].'</td><td style="background:'.$ColorCelda[1][$i][2].'">'.$pronostico[1]["P".$a."2"].'<td style="background:'.$ColorCelda[1][$i][3].'">'.$desarrollo[1][0].'</td><td style="background:'.$ColorCelda[1][$i][4].'">'.$desarrollo[1][1].'</td>
						<td style="background:'.$ColorCelda[2][$i][0].'">'.$pronostico[2]["P".$a."1"].'</td><td style="background:'.$ColorCelda[2][$i][1].'">'.$pronostico[2]["P".$a."X"].'</td><td style="background:'.$ColorCelda[2][$i][2].'">'.$pronostico[2]["P".$a."2"].'<td style="background:'.$ColorCelda[2][$i][3].'">'.$desarrollo[2][0].'</td><td style="background:'.$ColorCelda[2][$i][4].'">'.$desarrollo[2][1].'</td>
						<td style="background:'.$ColorCelda[3][$i][0].'">'.$pronostico[3]["P".$a."1"].'</td><td style="background:'.$ColorCelda[3][$i][1].'">'.$pronostico[3]["P".$a."X"].'</td><td style="background:'.$ColorCelda[3][$i][2].'">'.$pronostico[3]["P".$a."2"].'<td style="background:'.$ColorCelda[3][$i][3].'">'.$desarrollo[3][0].'</td><td style="background:'.$ColorCelda[3][$i][4].'">'.$desarrollo[3][1].'</td>
						<td style="background:'.$ColorCelda[4][$i][0].'">'.$pronostico[4]["P".$a."1"].'</td><td style="background:'.$ColorCelda[4][$i][1].'">'.$pronostico[4]["P".$a."X"].'</td><td style="background:'.$ColorCelda[4][$i][2].'">'.$pronostico[4]["P".$a."2"].'<td style="background:'.$ColorCelda[4][$i][3].'">'.$desarrollo[4][0].'</td><td style="background:'.$ColorCelda[4][$i][4].'">'.$desarrollo[4][1].'</td>
						<td style="background:'.$ColorCelda[5][$i][0].'">'.$pronostico[5]["P".$a."1"].'</td><td style="background:'.$ColorCelda[5][$i][1].'">'.$pronostico[5]["P".$a."X"].'</td><td style="background:'.$ColorCelda[5][$i][2].'">'.$pronostico[5]["P".$a."2"].'<td style="background:'.$ColorCelda[5][$i][3].'">'.$desarrollo[5][0].'</td><td style="background:'.$ColorCelda[5][$i][4].'">'.$desarrollo[5][1].'</td>
						<td style="background:'.$ColorCelda[6][$i][0].'">'.$pronostico[6]["P".$a."1"].'</td><td style="background:'.$ColorCelda[6][$i][1].'">'.$pronostico[6]["P".$a."X"].'</td><td style="background:'.$ColorCelda[6][$i][2].'">'.$pronostico[6]["P".$a."2"].'<td style="background:'.$ColorCelda[6][$i][3].'">'.$desarrollo[6][0].'</td><td style="background:'.$ColorCelda[6][$i][4].'">'.$desarrollo[6][1].'</td>
						<td style="background:'.$ColorCelda[7][$i][0].'">'.$pronostico[7]["P".$a."1"].'</td><td style="background:'.$ColorCelda[7][$i][1].'">'.$pronostico[7]["P".$a."X"].'</td><td style="background:'.$ColorCelda[7][$i][2].'">'.$pronostico[7]["P".$a."2"].'<td style="background:'.$ColorCelda[7][$i][3].'">'.$desarrollo[7][0].'</td><td style="background:'.$ColorCelda[7][$i][4].'">'.$desarrollo[7][1].'</td></tr>';
			}
			else
			{
				$str = '<tr><td style="background:gray;color:red;font-weight:bold">P-15</td><td style="background:gray;color:red;font-weight:bold">'.$partido["P15"].'</td><td style="color:red">'.$golesr1.'-'.$golesr2.'
						<td colspan="6" style="color:red;background:'.$ColorCelda[0][14][0].'">'.$pronostico[0]["PF1"].' - '.$pronostico[0]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[1][14][0].'">'.$pronostico[1]["PF1"].' - '.$pronostico[1]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[2][14][0].'">'.$pronostico[2]["PF1"].' - '.$pronostico[2]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[3][14][0].'">'.$pronostico[3]["PF1"].' - '.$pronostico[3]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[4][14][0].'">'.$pronostico[4]["PF1"].' - '.$pronostico[4]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[5][14][0].'">'.$pronostico[5]["PF1"].' - '.$pronostico[5]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[6][14][0].'">'.$pronostico[6]["PF1"].' - '.$pronostico[6]["PF2"].'</td>
						<td colspan="6" style="color:red;background:'.$ColorCelda[7][14][0].'">'.$pronostico[7]["PF1"].' - '.$pronostico[7]["PF2"].'</td></tr>';		
			}
			//echo utf8_encode($str);
			echo ($str);
		}
		
	}
	if($todosesc=='todos')
	{
		$str = '<tr style="color:green;font-weight:bold"><td colspan="3" align="right" style="background:gray">SUMA DE ACIERTOS:</td>
				<td colspan="3">'.$sumaciertosFerA.'</td>
				<td colspan="3">'.$sumaciertosMicA.'</td>
				<td colspan="3">'.$sumaciertosJulG.'</td>
				<td colspan="3">'.$sumaciertosJavM.'</td>
				<td colspan="3">'.$sumaciertosFerG.'</td>
				<td colspan="3">'.$sumaciertosFelG.'</td>
				<td colspan="3">'.$sumaciertosAntL.'</td>
				<td colspan="3">'.$sumaciertosAlvL.'</td>
				<td colspan="3">'.$sumaciertosComu.'</td></tr></table>';
		echo $str;
	}
	else
	{
		//Fila de aciertos
		$str = '<tr><td colspan="4" style="background:gray;color:green;font-weight:bold">ACIERTOS:</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosFerA.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosMicA.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosJulG.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosJavM.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosFerG.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosFelG.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosAntL.'</td>
				<td colspan="6" style="color:green;font-weight:bold">'.$sumaciertosAlvL.'</td></tr>';
		echo $str;
		
		//Fila de premios
		for($i=0;$i<8;$i++)
		{
			if($aciertopremio1[$i]==14)
			{
				$trinque[$i]=$resultado["P2"];
				if($ColorCelda[$i][14][0]=='green')
					$trinque[$i]=$resultado["P1"];
			}					
			else if($aciertopremio1[$i]==13)
				$trinque[$i]=$resultado["P3"];
			else if($aciertopremio1[$i]==12)
				$trinque[$i]=$resultado["P4"];
			else if($aciertopremio1[$i]==11)
				$trinque[$i]=$resultado["P5"];
			else if($aciertopremio1[$i]==10)
				$trinque[$i]=$resultado["P6"];
					
			if($aciertopremio2[$i]==14)
			{
				$trinque[$i]+=$resultado["P2"];
				if($ColorCelda[$i][14][0]=='green')
					$trinque[$i]=$resultado["P1"];
			}
			else if($aciertopremio2[$i]==13)
				$trinque[$i]+=$resultado["P3"];
			else if($aciertopremio2[$i]==12)
				$trinque[$i]+=$resultado["P4"];
			else if($aciertopremio2[$i]==11)
				$trinque[$i]+=$resultado["P5"];
			else if($aciertopremio2[$i]==10)
				$trinque[$i]+=$resultado["P6"];
		}
		$str = '<tr><td colspan="4" style="background:gray;color:blue;font-weight:bold">PREMIOS: (EUROS)</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[0].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[1].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[2].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[3].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[4].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[5].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[6].'</td>
				<td colspan="6" style="color:blue;font-weight:bold">'.$trinque[7].'</td></tr></table><br />';
		echo $str;
		
		//Tabla comunitaria
			//Arrays de triples
		$Singosdestriples1=array('X','2','1','X','2','1','1','1',
							     'X','X','X','2','2','2','1','1',
							     '1','X','2','1','1','X','X','X',
							     'X','2','1','X','2','1','X','2',
							     '1','X','2','1','X','2','1','X',
							     '2','1','X','2','1','1','1','1',
							     '1','1','X','X','X','2','2','2',
							     '2','2','2','X','2','2','2','1');
								 
		$Singosdestriples2=array('X','X','2','2','2','1','X','2',
							     '1','X','2','1','X','2','1','X',
							     '1','1','1','X','2','1','X','2',
							     '1','1','X','X','X','2','2','2',
							     '1','1','1','1','1','1','2','2',
							     'X','2','1','X','2','2','2','2',
							     '2','2','1','X','X','X','X','X',
							     'X','X','X','2','1','X','2','1');
			//Arrays de dobles
		$Singosdesdobles=array(array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,1,0,1,1,1,0,0,1,1,1,1,1,1,1,1,0,1,0,0,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1),
							   array(0,1,1,1,0,0,1,1,0,0,1,1,1,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,0,0,0,1,1,1,0,1,1,0,0,0,0,0,0,0,1,1,1,0,0,0,0,1,1,1,1,1,0,0,0,1,1,1,1,1),
							   array(0,0,0,1,0,0,1,0,1,0,1,1,0,0,0,0,0,0,1,1,0,0,0,0,1,0,0,1,1,1,0,1,1,0,0,1,0,0,1,0,1,1,1,1,0,0,0,1,1,1,1,1,1,0,0,0,1,1,1,1,1,0,1,1),
							   array(0,0,0,0,1,0,0,1,1,1,1,1,0,0,1,1,0,1,0,1,1,0,1,1,0,0,0,0,0,0,0,0,0,1,1,1,0,1,1,0,0,1,1,1,0,1,1,0,1,1,0,1,1,0,0,1,0,0,1,0,1,1,1,0),
							   array(1,0,1,0,0,0,1,0,0,1,1,1,0,1,1,0,0,0,1,0,1,1,0,1,1,1,1,1,0,1,0,1,0,0,1,1,1,0,1,0,0,0,0,1,1,1,0,1,1,0,1,1,0,0,0,1,0,0,1,0,0,1,0,0),
							   array(0,0,0,0,0,1,1,1,0,0,1,0,1,1,1,0,0,0,0,0,0,1,1,1,1,0,1,0,0,0,0,1,1,0,0,1,1,1,0,1,1,1,0,1,0,0,1,0,0,1,1,0,1,0,1,1,0,1,1,1,1,1,0,0));

		$sel = $con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada);
		$procom=$sel->fetch_assoc();

		echo '<table style="font-family:arial" border="1">';
		$str = '<tr style="background:gray;color:orange;"><th colspan="73" style="padding-right:10px;padding-left:10px">LA COMUNITARIA';
		echo $str;
		$str = '<tr style="background:gray;color:blue;"><th colspan="73" style="padding-right:10px;padding-left:10px">AUTOR: ';
		echo $str;
		/*if($procom['Usuario']=='fermina') $str='Fermín Aldaz</th></tr>';
		else if($procom['Usuario']=='michel') $str='Michel Aldaz</th></tr>';
		else if($procom['Usuario']=='julio') $str='Julio García-Mina</th></tr>';
		else if($procom['Usuario']=='javier') $str='Javier Marco</th></tr>';
		else if($procom['Usuario']=='ferming') $str='Fermín García-Mina</th></tr>';
		else if($procom['Usuario']=='felipe') $str='Felipe García-Mina</th></tr>';
		else if($procom['Usuario']=='antonio') $str='Antonio Luna</th></tr>';
		else if($procom['Usuario']=='alvaro') $str='Álvaro Luna</th></tr>';*/
		if($restodediv==7) $str='Fermín Aldaz</th></tr>';
		else if($restodediv==0) $str='Michel Aldaz</th></tr>';
		else if($restodediv==1) $str='Julio García-Mina</th></tr>';
		else if($restodediv==2) $str='Javier Marco</th></tr>';
		else if($restodediv==3) $str='Fermín García-Mina</th></tr>';
		else if($restodediv==4) $str='Felipe García-Mina</th></tr>';
		else if($restodediv==5) $str='Antonio Luna</th></tr>';
		else if($restodediv==6) $str='Álvaro Luna</th></tr>';
		echo $str;
		
		$str = '<tr style="background:gray;color:orange;">
				<th rowspan="2" style="padding-right:10px;padding-left:10px">Nº</th>
				<th rowspan="2" style="padding-right:150px;padding-left:150px">PARTIDO</th>
				<th rowspan="17" style="background:gray;padding-left:2px"></th>
				<th rowspan="2">Escr.</th>
				<th rowspan="18" style="background:gray;padding-left:2px"></th>
				<th colspan="3" style="padding-right:10px;padding-left:10px">Pronos.</th>
				<th rowspan="18" style="background:gray;padding-left:2px"></th>
				<th colspan="64" style="padding-right:10px;padding-left:10px">Desarrollo</th></tr>';
		echo $str;
		$str = '<tr style="background:gray;color:orange;">
				<th style="padding-right:10px;padding-left:10px">1</th>
				<th style="padding-right:10px;padding-left:10px">X</th>
				<th style="padding-right:10px;padding-left:10px">2</th>';
		echo $str;
		for($j=1;$j<65;$j++)
			echo '<th>'.$j.'</th>';
		echo '</tr>';

		$aciertos=0;
		for($j=0;$j<64;$j++)
			$aciertosdesarrollo[$j]=0;
		//Los partidos normales
		$numerodetriple=0;
		$numerodedoble=0;
		for($i=1;$i<15;$i++)
		{
			if($i==10)
				$a='A';
			else if($i==11)
				$a='B';
			else if($i==12)
				$a='C';
			else if($i==13)
				$a='D';
			else if($i==14)
				$a='E';
			else
				$a=$i;
			
			//Convierto checkeds en exises
			if($procom["P".$a."1C"]=='checked')
				$procom["P".$a."1C"]='X';
			if($procom["P".$a."XC"]=='checked')
				$procom["P".$a."XC"]='X';
			if($procom["P".$a."2C"]=='checked')
				$procom["P".$a."2C"]='X';

			$coloracierto[0]='white';
			$coloracierto[1]='white';
			$coloracierto[2]='white';

			if($resultado["R".$i]=='1' && $procom["P".$a."1C"]=='X')
			{
				$coloracierto[0]='green';
				$aciertos++;
			}
			else if($resultado["R".$i]=='X' && $procom["P".$a."XC"]=='X')
			{
				$coloracierto[1]='green';
				$aciertos++;
			}
			else if($resultado["R".$i]=='2' && $procom["P".$a."2C"]=='X')
			{
				$coloracierto[2]='green';
				$aciertos++;
			}
			
			for($k=0;$k<64;$k++)
			{
				$signodesarrollo[$k]="";
				$ColorCeldaDesarrollo[$k]='white';
			}
			$casillasmarcadas=0;
			$marcadosencillo='0';
			$marcadodoble[0]='0';
			$marcadodoble[1]='0';
			if($procom["P".$a."1C"]=='X')
			{
				$casillasmarcadas++;
				$marcadosencillo='1';
				$marcadodoble[0]='1';
			}
			if($procom["P".$a."XC"]=='X')
			{
				$casillasmarcadas++;
				$marcadosencillo='X';
				if($marcadodoble[0]=='1')
					$marcadodoble[1]='X';
				else
					$marcadodoble[0]='X';
			}
			if($procom["P".$a."2C"]=='X')
			{
				$casillasmarcadas++;
				$marcadosencillo='2';
				$marcadodoble[1]='2';
			}
		
			if($ano['anoencurso']<2018 ||($ano['anoencurso']==2018 && $jornada<=6))
			{
				if($casillasmarcadas==3)
				{
					$numerodetriple++;
					for($k=0;$k<64;$k++)
					{
						if($numerodetriple==1)
							$signodesarrollo[$k]=$Singosdestriples1[$k];
						else
							$signodesarrollo[$k]=$Singosdestriples2[$k];
						if($resultado["R".$i]==$signodesarrollo[$k])
						{
							$ColorCeldaDesarrollo[$k]='green';
							$aciertosdesarrollo[$k]++;
						}
					}
				}
				else if($casillasmarcadas==2)
				{
					for($k=0;$k<64;$k++)
					{
						$signodesarrollo[$k]=$marcadodoble[$Singosdesdobles[$numerodedoble][$k]];
						if($resultado["R".$i]==$signodesarrollo[$k])
						{
							$ColorCeldaDesarrollo[$k]='green';
							$aciertosdesarrollo[$k]++;
						}
					}
					$numerodedoble++;
				}
				else
				{
					for($k=0;$k<64;$k++)
						$signodesarrollo[$k]=$marcadosencillo;
					if($resultado["R".$i]==$marcadosencillo)
					{
						for($k=0;$k<64;$k++)
						{
							$ColorCeldaDesarrollo[$k]='green';
							$aciertosdesarrollo[$k]++;
						}
					}
				}
			}
			else
			{
				if($casillasmarcadas==1)
				{
					for($k=0;$k<64;$k++)
						$signodesarrollo[$k]=$marcadosencillo;
					if($resultado["R".$i]==$marcadosencillo)
					{
						for($k=0;$k<64;$k++)
						{
							$ColorCeldaDesarrollo[$k]='green';
							$aciertosdesarrollo[$k]++;
						}
					}
				}
				else if($casillasmarcadas==2)
				{
					$divisor=1;
					switch($numerodedoble)
					{
						case 0:
							$divisor=32;
							break;
						case 1:
							$divisor=16;
							break;
						case 2:
							$divisor=8;
							break;
						case 3:
							$divisor=4;
							break;
						case 4:
							$divisor=2;
							break;
						case 5:
							$divisor=1;
							break;
					}
					$tocacolor[0]='white';
					$tocacolor[1]='white';
					if($resultado["R".$i]==$marcadodoble[0])
						$tocacolor[0]='green';
					else if($resultado["R".$i]==$marcadodoble[1])
						$tocacolor[1]='green';
					for($k=0;$k<64;$k++)
					{
						if(($k/$divisor)%2==0)
						{
							$signodesarrollo[$k]=$marcadodoble[0];
							$ColorCeldaDesarrollo[$k]=$tocacolor[0];
							if($tocacolor[0]=='green')
								$aciertosdesarrollo[$k]++;
						}
						else
						{
							$signodesarrollo[$k]=$marcadodoble[1];
							$ColorCeldaDesarrollo[$k]=$tocacolor[1];
							if($tocacolor[1]=='green')
								$aciertosdesarrollo[$k]++;
						}									
					}
					$numerodedoble++;
				}
			}
	
			$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$i.'</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P".$i].'</td><td style="color:gray">'.$resultado["R".$i].'
					<td style="background:'.$coloracierto[0].'">'.$procom["P".$a."1C"].'</td>
					<td style="background:'.$coloracierto[1].'">'.$procom["P".$a."XC"].'</td>
					<td style="background:'.$coloracierto[2].'">'.$procom["P".$a."2C"].'</td>';
			//echo utf8_encode($str);
			echo ($str);
			for($k=0;$k<64;$k++)
				echo '<td style="background:'.$ColorCeldaDesarrollo[$k].'">'.$signodesarrollo[$k].'</td>';
			echo '</tr>';
		}
		//El pleno al 15
		$coloracierto='white';
		if($resultado["R15_1"]==$procom["PF1C"] && $resultado["R15_2"]==$procom["PF2C"])
		{
			$coloracierto='green';
			if($aciertos==14)
				$aciertos=15;
			for($i=0;$i<64;$i++)
			{
				if($aciertosdesarrollo[$i]==14)
					$aciertosdesarrollo[$i]=15;
			}
		}
		//Fila del pleno al 15
		$str = '<tr><td style="background:gray;color:red;font-weight:bold">P-15</td>
				<td style="background:gray;color:red;font-weight:bold">'.$partido["P15"].'</td>
				<td style="color:red">'.$golesr1.'-'.$golesr2.'
				<td colspan="3" style="color:red;background:'.$coloracierto.'">'.$procom["PF1C"].' - '.$procom["PF2C"].'</td>
				<td colspan="64" style="color:red;background:'.$coloracierto.'">'.$procom["PF1C"].' - '.$procom["PF2C"].'</td>
				</tr>';
		//echo utf8_encode($str);
		echo ($str);
		//Fila de aciertos
		$str = '<tr><td colspan="4" style="background:gray;color:green;font-weight:bold">ACIERTOS:</td>
				<td colspan="3" style="color:green;font-weight:bold">'.$aciertos.'</td>';
		echo ($str);		
		for($i=0;$i<64;$i++)
		{
			$colorceldadesarrollo[$i]='gray';
			if($aciertosdesarrollo[$i]==15)
				$colorceldadesarrollo[$i]='red';
			if($aciertosdesarrollo[$i]==14)
				$colorceldadesarrollo[$i]='darkmagenta';
			if($aciertosdesarrollo[$i]==13)
				$colorceldadesarrollo[$i]='blue';
			if($aciertosdesarrollo[$i]==12)
				$colorceldadesarrollo[$i]='orange';
			if($aciertosdesarrollo[$i]==11)
				$colorceldadesarrollo[$i]='magenta';
			if($aciertosdesarrollo[$i]==10)
				$colorceldadesarrollo[$i]='green';
			echo '<td style="color:'.$colorceldadesarrollo[$i].';font-weight:bold">'.$aciertosdesarrollo[$i].'</td>';
		}
		echo '</tr>';
		//Fila de premios
		$premiosdesarrollo=0;
		for($i=0;$i<64;$i++)
		{
			if($aciertosdesarrollo[$i]==15)
				$premiosdesarrollo+=($resultado["P1"]+$resultado["P2"]); //Si hay pleno al 15 también se cobra una de catorce
			if($aciertosdesarrollo[$i]==14)
				$premiosdesarrollo+=$resultado["P2"];
			if($aciertosdesarrollo[$i]==13)
				$premiosdesarrollo+=$resultado["P3"];
			if($aciertosdesarrollo[$i]==12)
				$premiosdesarrollo+=$resultado["P4"];
			if($aciertosdesarrollo[$i]==11)
				$premiosdesarrollo+=$resultado["P5"];
			if($aciertosdesarrollo[$i]==10)
				$premiosdesarrollo+=$resultado["P6"];
		}
		$premiosdesarrollo=number_format ( $premiosdesarrollo , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$str = '<tr><td colspan="9" style="background:gray;color:blue;font-weight:bold">PREMIOS: (EUROS)</td>
				<td colspan="64" style="color:blue;font-weight:bold">'.$premiosdesarrollo.'</td></tr></table><br />';
		echo $str;
	}
}
?>
