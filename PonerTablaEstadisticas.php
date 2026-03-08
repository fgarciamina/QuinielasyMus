<?php
$IndiceAno=$_GET["p"];
$nombre=$_GET["q"];

error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

if($IndiceAno==0)
{
	$sel=$con->query("SELECT * FROM Ano WHERE 1");
	$anosel = $sel->fetch_assoc();
	$ano=$anosel['anoencurso'];
	$IndiceAno=$ano-2011;
}
else
{
	$IndiceAno--;
	$ano=$IndiceAno+2011;
}
echo $IndiceAno.'|$|'; 

if($nombre=='Alfonso' || $nombre=='Caixero' || $nombre=='Fermin' || $nombre=='Manolin') //Caso Quiniela Manolin
{
	$n=4;
	$cartelito[0]='Alfonso Martínez Sánchez-Sicilia';
	$cartelito[1]='Fermín García-Mina Cabredo';
	$cartelito[2]='Fernando Sánchez Rodríguez';
	$cartelito[3]='Manuel Ibáñez Macías';
	
	$usuario[0]='alfonso';
	$usuario[1]='fermin';
	$usuario[2]='caixero';
	$usuario[3]='manolin';
	
	$casocomputa='Computa';
}
else //Caso Quiniela Mina
{
	$n=8;
	$cartelito[0]='Fermín Aldaz García-Mina';
	$cartelito[1]='Michel Aldaz García-Mina';
	$cartelito[2]='Julio García-Mina Cabredo';
	$cartelito[3]='Javier Marco García-Mina';
	$cartelito[4]='Fermín García-Mina Cabredo';
	$cartelito[5]='Felipe García-Mina Cabredo';
	$cartelito[6]='Antonio Luna García-Mina';
	$cartelito[7]='Álvaro Luna García-Mina';
	
	$usuario[0]='fermina';
	$usuario[1]='michel';
	$usuario[2]='julio';
	$usuario[3]='javier';
	$usuario[4]='ferming';
	$usuario[5]='felipe';
	$usuario[6]='antonio';
	$usuario[7]='alvaro';
	
	$casocomputa='ComputaMina';
}

//******************************************************** PRIMERA VUELTA *******************************************************************
echo '<table style="font-family:arial" border="1">';
$str = '<tr style="background:gray;color:orange;"><th colspan="24">Primera Vuelta</th></tr>';
echo ($str);
$str = '<tr style="background:gray;color:orange;"><th rowspan="2">Participante</th>
		<th colspan="19">Jornada</th><th rowspan="2">Suma Aciertos</th>
		<th rowspan="2">Suma Premios<br />(Euros)</th>
		<th rowspan="2">Restas Excep.<br />(Euros)</th>
		<th rowspan="2">Sumas Excep.<br />(Euros)</th></tr>';
echo ($str);
$str = '<tr style="background:gray;color:orange;"><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th><th>13</th><th>14</th><th>15</th><th>16</th><th>17</th><th>18</th><th>19</th></tr>';
echo ($str);

$totalpasta=0;
for ($i=0;$i<$n;$i++)
{
	$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$cartelito[$i].'</td>';
	echo ($str);

	$sumapastaordin=0;
	$sumapastaloca=0;
	$sumaaciertos=0;
	$sumaaciertoscom=0;
	$sumarestas=0;
	$sumasumas=0;
	for($iter=1;$iter<20;$iter++)
	{
		$sel=$con->query("SELECT * FROM Computa WHERE Jornada='".$iter."'");
		$computasino = $sel->fetch_assoc();
		$sel=$con->query("SELECT * FROM Resultados WHERE Ano='".$ano."' AND Usuario='".$usuario[$i]."' AND Jornada='".$iter."'");
		$aciertos = $sel->fetch_assoc();
		if($computasino[$casocomputa]==1)
		{
			$sumaaciertos+=$aciertos["Aciertos"];
			$sumaaciertoscom+=$aciertos["AciertosComun"];
			$ColorCasilla='white';
		}
		else
			$ColorCasilla='red';
		if($aciertos["Aciertos"]=='')
			$str='<td style="background:'.$ColorCasilla.'">0</td>';
		else
			$str='<td style="background:'.$ColorCasilla.'">'.$aciertos["Aciertos"].'</td>';
		echo ($str);
		$sumapastaordin+=$aciertos["Premios"];
		$sumapastaloca+=$aciertos["Loca"];
		$sumarestas+=$aciertos["Restas"];
		$sumasumas+=$aciertos["Sumas"];
	}
	$totalaciertospv[$i]=$sumaaciertos;
	$totalcomun[$i]=$sumaaciertoscom;
	$totalaciertos[$i]=$sumaaciertos+$sumaaciertoscom;
	$totalpastaordinpv[$i]=$sumapastaordin;
	$totalpastalocapv[$i]=$sumasumas+$sumapastaloca;
	$totalpasta+=($totalpastaordinpv[$i]+$totalpastalocapv[$i]);
	$totaldispuestopv[$i]=$sumarestas;
	$sumapasta=number_format ( $totalpastaordinpv[$i] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$sumarestas=number_format ( $sumarestas , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$sumasumas=number_format ( $sumasumas , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$str='<td>'.$sumaaciertos.'</td><td>'.$sumapasta.'</td><td style="color:red">'.$sumarestas.'</td><td style="color:green">'.$sumasumas.'</tr>';
	echo ($str);

	$puesto[$i][0]=$cartelito[$i];
	$puesto[$i][1]=$totalaciertospv[$i];
	$puesto[$i][2]=$totalpastaordinpv[$i];
	$puesto[$i][3]=$totalpastalocapv[$i];
	$puesto[$i][4]=$totalpastaordinpv[$i]+$totalpastalocapv[$i];
	$puesto[$i][5]=$totalcomun[$i];
	$puesto[$i][6]=$totalaciertos[$i];
	//Burbuja de puestos
	if($i>0)
	{
		for($j=$i;$j>0;$j--)
		{
			if(($puesto[$j][6]>$puesto[$j-1][6]) || ($puesto[$j][6]==$puesto[$j-1][6] && $puesto[$j][4]>$puesto[$j-1][4]))
			{
				$puestoaux[0]=$puesto[$j-1][0];
				$puestoaux[1]=$puesto[$j-1][1];
				$puestoaux[2]=$puesto[$j-1][2];
				$puestoaux[3]=$puesto[$j-1][3];
				$puestoaux[4]=$puesto[$j-1][4];
				$puestoaux[5]=$puesto[$j-1][5];
				$puestoaux[6]=$puesto[$j-1][6];
				
				$puesto[$j-1][0]=$puesto[$j][0];
				$puesto[$j-1][1]=$puesto[$j][1];
				$puesto[$j-1][2]=$puesto[$j][2];
				$puesto[$j-1][3]=$puesto[$j][3];
				$puesto[$j-1][4]=$puesto[$j][4];
				$puesto[$j-1][5]=$puesto[$j][5];
				$puesto[$j-1][6]=$puesto[$j][6];
				
				$puesto[$j][0]=$puestoaux[0];
				$puesto[$j][1]=$puestoaux[1];
				$puesto[$j][2]=$puestoaux[2];
				$puesto[$j][3]=$puestoaux[3];
				$puesto[$j][4]=$puestoaux[4];
				$puesto[$j][5]=$puestoaux[5];
				$puesto[$j][6]=$puestoaux[6];
			}
		}
	}
}
$str='</table></br >';
echo ($str);

if($n==4)
{
	for($i=0;$i<4;$i++)
	{
		$puesto[$i][2]=number_format ( $puesto[$i][2] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$puesto[$i][3]=number_format ( $puesto[$i][3] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$puesto[$i][4]=number_format ( $puesto[$i][4] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	}
}
else
{
	for($i=0;$i<8;$i++)
	{
		$puesto[$i][2]=number_format ( $puesto[$i][2] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$puesto[$i][3]=number_format ( $puesto[$i][3] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$puesto[$i][4]=number_format ( $puesto[$i][4] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	}
}

if($n==4)
{
	echo '<table style="font-family:arial" border="1">';
	$str = '<tr style="background:gray;color:orange;"><th colspan="6">Campeonato de invierno</th></tr>';
	echo ($str);
	$str = '<tr style="background:gray;color:orange;"><th rowspan="2">Puesto</th><th rowspan="2">Participante</th><th rowspan="2">Puntos</th><th colspan="3">Trinque</th>';
	echo ($str);
	$str='<tr style="background:gray;color:orange;"><th>Ordin.</th><th>Por Loca</th><th>SUMA</th></tr>';
	echo ($str);
}
else
{
	echo '<table style="font-family:arial" border="1">';
	$str = '<tr style="background:gray;color:orange;"><th colspan="8">Campeonato de invierno</th></tr>';
	echo ($str);
	$str = '<tr style="background:gray;color:orange;"><th rowspan="2">Puesto</th><th rowspan="2">Participante</th><th rowspan="2">Puntos</th><th rowspan="2">Puntos Com</th><th rowspan="2">TOTAL</th><th colspan="3">Trinque</th>';
	echo ($str);
	$str='<tr style="background:gray;color:orange;"><th>Ordin.</th><th>Por Común</th><th>SUMA</th></tr>';
	echo ($str);
}

if($n==8)
{
	$str = '<tr><td style="background:gray;color:blue;font-weight:bold">Primer Clasificado</td><td style="color:blue">'.$puesto[0][0].'</td><td style="color:blue">'.$puesto[0][1].'</td><td style="color:blue">'.$puesto[0][5].'</td><td style="color:blue">'.$puesto[0][6].'</td><td style="color:blue">'.$puesto[0][2].'</td><td style="color:blue">'.$puesto[0][3].'</td><td style="color:blue">'.$puesto[0][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Segundo Clasificado</td><td>'.$puesto[1][0].'</td><td>'.$puesto[1][1].'</td><td>'.$puesto[1][5].'</td><td>'.$puesto[1][6].'</td><td>'.$puesto[1][2].'</td><td>'.$puesto[1][3].'</td><td>'.$puesto[1][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Tercer Clasificado</td><td>'.$puesto[2][0].'</td><td>'.$puesto[2][1].'</td><td>'.$puesto[2][5].'</td><td>'.$puesto[2][6].'</td><td>'.$puesto[2][2].'</td><td>'.$puesto[2][3].'</td><td>'.$puesto[2][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Cuarto Clasificado</td><td>'.$puesto[3][0].'</td><td>'.$puesto[3][1].'</td><td>'.$puesto[3][5].'</td><td>'.$puesto[3][6].'</td><td>'.$puesto[3][2].'</td><td>'.$puesto[3][3].'</td><td>'.$puesto[3][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Quinto Clasificado</td><td>'.$puesto[4][0].'</td><td>'.$puesto[4][1].'</td><td>'.$puesto[4][5].'</td><td>'.$puesto[4][6].'</td><td>'.$puesto[4][2].'</td><td>'.$puesto[4][3].'</td><td>'.$puesto[4][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Sexto Clasificado</td><td>'.$puesto[5][0].'</td><td>'.$puesto[5][1].'</td><td>'.$puesto[5][5].'</td><td>'.$puesto[5][6].'</td><td>'.$puesto[5][2].'</td><td>'.$puesto[5][3].'</td><td>'.$puesto[5][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Séptimo Clasificado</td><td>'.$puesto[6][0].'</td><td>'.$puesto[6][1].'</td><td>'.$puesto[6][5].'</td><td>'.$puesto[6][6].'</td><td>'.$puesto[6][2].'</td><td>'.$puesto[6][3].'</td><td>'.$puesto[6][4].'</td></tr>
			<tr><td style="background:gray;color:red;font-weight:bold">Farolillo Rojo</td><td style="color:red">'.$puesto[7][0].'</td><td style="color:red">'.$puesto[7][1].'</td><td style="color:red">'.$puesto[7][5].'</td><td style="color:red">'.$puesto[7][6].'</td><td style="color:red">'.$puesto[7][2].'</td><td style="color:red">'.$puesto[7][3].'</td><td style="color:red">'.$puesto[7][4].'</td></tr>';
	echo ($str);
}
else
{
	$str = '<tr><td style="background:gray;color:blue;font-weight:bold">Primer Clasificado</td><td style="color:blue">'.$puesto[0][0].'</td><td style="color:blue">'.$puesto[0][1].'</td><td style="color:blue">'.$puesto[0][2].'</td><td style="color:blue">'.$puesto[0][3].'</td><td style="color:blue">'.$puesto[0][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Segundo Clasificado</td><td>'.$puesto[1][0].'</td><td>'.$puesto[1][1].'</td><td>'.$puesto[1][2].'</td><td>'.$puesto[1][3].'</td><td>'.$puesto[1][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Tercer Clasificado</td><td>'.$puesto[2][0].'</td><td>'.$puesto[2][1].'</td><td>'.$puesto[2][2].'</td><td>'.$puesto[2][3].'</td><td>'.$puesto[2][4].'</td></tr>
			<tr><td style="background:gray;color:red;font-weight:bold">Farolillo Rojo</td><td style="color:red">'.$puesto[3][0].'</td><td style="color:red">'.$puesto[3][1].'</td><td style="color:red">'.$puesto[3][2].'</td><td style="color:red">'.$puesto[3][3].'</td><td style="color:red">'.$puesto[3][4].'</td></tr>';
	echo ($str);
}
	
$auxtotalpasta=number_format ( $totalpasta , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$str='<tr style="color:green;"><td style="background:gray;font-weight:bold">Recaudación 1ª vuelta:</td><td colspan="5">'.$auxtotalpasta.'.-Euros</td></tr></table><br />';
echo ($str);

if($n==8 && $ano<2024) //Tabla calsificación estilo antiguo, solo para Quiniela Mina y si el año es anterior a 2024
{
	for ($i=0;$i<8;$i++)
	{
		$sumaaciertos=0;
	
		for($iter=1;$iter<20;$iter++)
		{
			$sel=$con->query("SELECT * FROM Computa WHERE Jornada='".$iter."'");
			$computasino = $sel->fetch_assoc();
			$sel=$con->query("SELECT * FROM AciertosAnt WHERE Ano='".$ano."' AND Usuario='".$usuario[$i]."' AND Jornada='".$iter."'");
			$aciertos = $sel->fetch_assoc();
			if($computasino["ComputaMina"]==1)
				$sumaaciertos+=$aciertos["AciertosAnt"];
		}
		$totalaciertospvea[$i]=$sumaaciertos;
		$puesto[$i][0]=$cartelito[$i];
		$puesto[$i][1]=$totalaciertospvea[$i];
	
		//Burbuja de puestos
		if($i>0)
		{
			for($j=$i;$j>0;$j--)
			{
				if(($puesto[$j][1]>$puesto[$j-1][1]))
				{
					$puestoaux[0]=$puesto[$j-1][0];
					$puestoaux[1]=$puesto[$j-1][1];
					
					$puesto[$j-1][0]=$puesto[$j][0];
					$puesto[$j-1][1]=$puesto[$j][1];
					
					$puesto[$j][0]=$puestoaux[0];
					$puesto[$j][1]=$puestoaux[1];
				}
			}
		}
	}

	echo '<table style="font-family:arial" border="1">';
	$str = '<tr style="background:gray;color:orange;"><th colspan="3">Campeonato de invierno AL ESTILO ANTIGUO</th></tr>';
	echo ($str);
	$str = '<tr style="background:gray;color:orange;"><th>Puesto</th><th>Participante</th><th>Puntos</th></tr>';
	echo ($str);

	$str = '<tr><td style="background:gray;color:blue;font-weight:bold">Primer Clasificado</td><td style="color:blue">'.$puesto[0][0].'</td><td style="color:blue">'.$puesto[0][1].'</td></tr>
		    <tr><td style="background:gray;color:orange;font-weight:bold">Segundo Clasificado</td><td>'.$puesto[1][0].'</td><td>'.$puesto[1][1].'</td></tr>
		    <tr><td style="background:gray;color:orange;font-weight:bold">Tercer Clasificado</td><td>'.$puesto[2][0].'</td><td>'.$puesto[2][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Cuarto Clasificado</td><td>'.$puesto[3][0].'</td><td>'.$puesto[3][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Quinto Clasificado</td><td>'.$puesto[4][0].'</td><td>'.$puesto[4][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Sexto Clasificado</td><td>'.$puesto[5][0].'</td><td>'.$puesto[5][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Séptimo Clasificado</td><td>'.$puesto[6][0].'</td><td>'.$puesto[6][1].'</td></tr>
			<tr><td style="background:gray;color:red;font-weight:bold">Farolillo Rojo</td><td style="color:red">'.$puesto[7][0].'</td><td style="color:red">'.$puesto[7][1].'</td></tr></table></br>';
	echo ($str);
}

//******************************************************** SEGUNDA VUELTA *******************************************************************
echo '<table style="font-family:arial" border="1">';
$str = '<tr style="background:gray;color:orange;"><th colspan="24">Segunda Vuelta</th></tr>';
echo ($str);
$str = '<tr style="background:gray;color:orange;"><th rowspan="2">Participante</th><th colspan="19">Jornada</th><th rowspan="2">Suma Aciertos</th><th rowspan="2">Suma Premios<br />(Euros)</th><th rowspan="2">Restas Excep.<br />(Euros)</th><th rowspan="2">Sumas Excep.<br />(Euros)</th></tr>';
echo ($str);
$str = '<tr style="background:gray;color:orange;"><th>20</th><th>21</th><th>22</th><th>23</th><th>24</th><th>25</th><th>26</th><th>27</th><th>28</th><th>29</th><th>30</th><th>31</th><th>32</th><th>33</th><th>34</th><th>35</th><th>36</th><th>37</th><th>38</th></tr>';
echo ($str);

//Aquí no se pone totalpasta a 0 para que sea el acumulado de ambas vueltas
for ($i=0;$i<$n;$i++)
{
	$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$cartelito[$i].'</td>';
	echo ($str);

	$sumapastaordin=0;
	$sumapastaloca=0;
	$sumaaciertos=0;
	$sumaaciertoscom=0;
	$sumarestas=0;
	$sumasumas=0;
	for($iter=1;$iter<20;$iter++)
	{
		$jornadasv=$iter+19;
		$sel=$con->query("SELECT * FROM Computa WHERE Jornada='".$jornadasv."'");
		$computasino = $sel->fetch_assoc();
		$sel=$con->query("SELECT * FROM Resultados WHERE Ano='".$ano."' AND Usuario='".$usuario[$i]."' AND Jornada='".$jornadasv."'");
		$aciertos = $sel->fetch_assoc();
		if(!is_null($aciertos))
		{
			if($computasino[$casocomputa]==1)
			{
				$sumaaciertos+=$aciertos["Aciertos"];
				$sumaaciertoscom+=$aciertos["AciertosComun"];
				$ColorCasilla='white';
			}
			else
				$ColorCasilla='red';
			if($aciertos["Aciertos"]=='')
				$str='<td style="background:'.$ColorCasilla.'">0</td>';
			else
				$str='<td style="background:'.$ColorCasilla.'">'.$aciertos["Aciertos"].'</td>';
			echo ($str);
			$sumapastaordin+=$aciertos["Premios"];
			$sumapastaloca+=$aciertos["Loca"];
			$sumarestas+=$aciertos["Restas"];
			$sumasumas+=$aciertos["Sumas"];
		}
		else
		{
			$ColorCasilla='white';
			$str='<td style="background:'.$ColorCasilla.'">0</td>';
			echo ($str);
		}
	}
	$totalaciertossv[$i]=$sumaaciertos;
	$totalcomunsv[$i]=$sumaaciertoscom;
	$totalsv[$i]=$sumaaciertos+$sumaaciertoscom;
	$totalpastaordinsv[$i]=$sumapastaordin;
	$totalpastalocasv[$i]=$sumasumas+$sumapastaloca;
	$totalpasta+=($totalpastaordinsv[$i]+$totalpastalocasv[$i]);
	$sel=$con->query("SELECT * FROM Dispuesto WHERE Ano='".$ano."' AND Participante='".$usuario[$i]."'");
	$dispuesto = $sel->fetch_assoc();
	if(!is_null($dispuesto))
		$totaldispuesto[$i]=$sumarestas+$totaldispuestopv[$i]+$dispuesto["Cantidad"];
	else
		$totaldispuesto[$i]=$sumarestas+$totaldispuestopv[$i];
	$sumapasta=number_format ( $totalpastaordinsv[$i] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$sumarestas=number_format ( $sumarestas , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$sumasumas=number_format ( $sumasumas , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$str='<td>'.$sumaaciertos.'</td><td>'.$sumapasta.'</td><td style="color:red">'.$sumarestas.'</td><td style="color:green">'.$sumasumas.'</tr>';
	echo ($str);

	$puesto[$i][0]=$cartelito[$i];
	$puesto[$i][1]=$totalaciertossv[$i]+$totalaciertospv[$i];
	$puesto[$i][2]=$totalpastaordinsv[$i]+$totalpastaordinpv[$i];
	$puesto[$i][3]=$totalpastalocasv[$i]+$totalpastalocapv[$i];
	$puesto[$i][4]=$totalpastaordinsv[$i]+$totalpastalocasv[$i]+$totalpastaordinpv[$i]+$totalpastalocapv[$i];
	$puesto[$i][5]=$totalcomun[$i]+$totalcomunsv[$i];
	$puesto[$i][6]=$totalaciertos[$i]+$totalsv[$i];
	$puesto[$i][7]=$totaldispuesto[$i];

	//Burbuja de puestos
	if($i>0)
	{
		for($j=$i;$j>0;$j--)
		{
			if(($puesto[$j][6]>$puesto[$j-1][6]) || ($puesto[$j][6]==$puesto[$j-1][6] && $puesto[$j][4]>$puesto[$j-1][4]))
			{
				$puestoaux[0]=$puesto[$j-1][0];
				$puestoaux[1]=$puesto[$j-1][1];
				$puestoaux[2]=$puesto[$j-1][2];
				$puestoaux[3]=$puesto[$j-1][3];
				$puestoaux[4]=$puesto[$j-1][4];
				$puestoaux[5]=$puesto[$j-1][5];
				$puestoaux[6]=$puesto[$j-1][6];
				$puestoaux[7]=$puesto[$j-1][7];
				
				$puesto[$j-1][0]=$puesto[$j][0];
				$puesto[$j-1][1]=$puesto[$j][1];
				$puesto[$j-1][2]=$puesto[$j][2];
				$puesto[$j-1][3]=$puesto[$j][3];
				$puesto[$j-1][4]=$puesto[$j][4];
				$puesto[$j-1][5]=$puesto[$j][5];
				$puesto[$j-1][6]=$puesto[$j][6];
				$puesto[$j-1][7]=$puesto[$j][7];
				
				$puesto[$j][0]=$puestoaux[0];
				$puesto[$j][1]=$puestoaux[1];
				$puesto[$j][2]=$puestoaux[2];
				$puesto[$j][3]=$puestoaux[3];
				$puesto[$j][4]=$puestoaux[4];
				$puesto[$j][5]=$puestoaux[5];
				$puesto[$j][6]=$puestoaux[6];
				$puesto[$j][7]=$puestoaux[7];
			}
		}
	}

/*
//provisional  Sirve para ver los aciertos de las comunes
	$sel=$con->query("SELECT * FROM Pronosticoscom WHERE Usuario='".$usuario[$i]."'");
	while($auxsel = $sel->fetch_assoc())
	{
		$sumacomun=0;
		$sel2=$con->query("SELECT * FROM escrutinio WHERE Jornada=".$auxsel["Jornada"]);
		$proncom=$sel2->fetch_assoc();
		if(($auxsel["P11C"]=='checked' && $proncom["R1"]=='1') || ($auxsel["P1XC"]=='checked' && $proncom["R1"]=='X') || ($auxsel["P12C"]=='checked' && $proncom["R1"]=='2')) $sumacomun++;
		if(($auxsel["P21C"]=='checked' && $proncom["R2"]=='1') || ($auxsel["P2XC"]=='checked' && $proncom["R2"]=='X') || ($auxsel["P22C"]=='checked' && $proncom["R2"]=='2')) $sumacomun++;
		if(($auxsel["P31C"]=='checked' && $proncom["R3"]=='1') || ($auxsel["P3XC"]=='checked' && $proncom["R3"]=='X') || ($auxsel["P32C"]=='checked' && $proncom["R3"]=='2')) $sumacomun++;
		if(($auxsel["P41C"]=='checked' && $proncom["R4"]=='1') || ($auxsel["P4XC"]=='checked' && $proncom["R4"]=='X') || ($auxsel["P42C"]=='checked' && $proncom["R4"]=='2')) $sumacomun++;
		if(($auxsel["P51C"]=='checked' && $proncom["R5"]=='1') || ($auxsel["P5XC"]=='checked' && $proncom["R5"]=='X') || ($auxsel["P52C"]=='checked' && $proncom["R5"]=='2')) $sumacomun++;
		if(($auxsel["P61C"]=='checked' && $proncom["R6"]=='1') || ($auxsel["P6XC"]=='checked' && $proncom["R6"]=='X') || ($auxsel["P62C"]=='checked' && $proncom["R6"]=='2')) $sumacomun++;
		if(($auxsel["P71C"]=='checked' && $proncom["R7"]=='1') || ($auxsel["P7XC"]=='checked' && $proncom["R7"]=='X') || ($auxsel["P72C"]=='checked' && $proncom["R7"]=='2')) $sumacomun++;
		if(($auxsel["P81C"]=='checked' && $proncom["R8"]=='1') || ($auxsel["P8XC"]=='checked' && $proncom["R8"]=='X') || ($auxsel["P82C"]=='checked' && $proncom["R8"]=='2')) $sumacomun++;
		if(($auxsel["P91C"]=='checked' && $proncom["R9"]=='1') || ($auxsel["P9XC"]=='checked' && $proncom["R9"]=='X') || ($auxsel["P92C"]=='checked' && $proncom["R9"]=='2')) $sumacomun++;
		if(($auxsel["PA1C"]=='checked' && $proncom["R10"]=='1') || ($auxsel["PAXC"]=='checked' && $proncom["R10"]=='X') || ($auxsel["PA2C"]=='checked' && $proncom["R10"]=='2')) $sumacomun++;
		if(($auxsel["PB1C"]=='checked' && $proncom["R11"]=='1') || ($auxsel["PBXC"]=='checked' && $proncom["R11"]=='X') || ($auxsel["PB2C"]=='checked' && $proncom["R11"]=='2')) $sumacomun++;
		if(($auxsel["PC1C"]=='checked' && $proncom["R12"]=='1') || ($auxsel["PCXC"]=='checked' && $proncom["R12"]=='X') || ($auxsel["PC2C"]=='checked' && $proncom["R12"]=='2')) $sumacomun++;
		if(($auxsel["PD1C"]=='checked' && $proncom["R13"]=='1') || ($auxsel["PDXC"]=='checked' && $proncom["R13"]=='X') || ($auxsel["PD2C"]=='checked' && $proncom["R13"]=='2')) $sumacomun++;
		if(($auxsel["PE1C"]=='checked' && $proncom["R14"]=='1') || ($auxsel["PEXC"]=='checked' && $proncom["R14"]=='X') || ($auxsel["PE2C"]=='checked' && $proncom["R14"]=='2')) $sumacomun++;

		if($auxsel["PF1C"]==$proncom["R15_1"] && $auxsel["PF2C"]==$proncom["R15_2"]) $sumacomun++;

		$con->query("UPDATE Resultados SET AciertosComun = ".$sumacomun." WHERE Ano=2025 AND Jornada = ".$auxsel["Jornada"]." AND Usuario = '".$usuario[$i]."'");
	}

//fin provisional
*/
}
$str='</table></br >';
echo ($str);
/*
if($n==4)
{
	for($i=0;$i<4;$i++)
	{
		$auxpuesto[$i][2]=number_format ( $puesto[$i][2] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$auxpuesto[$i][3]=number_format ( $puesto[$i][3] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$auxpuesto[$i][4]=number_format ( $puesto[$i][4] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$auxpuesto[$i][5]=number_format ( $puesto[$i][7] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	}
}
else
{
	for($i=0;$i<8;$i++)
	{
		$auxpuesto[$i][2]=number_format ( $puesto[$i][2] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$auxpuesto[$i][3]=number_format ( $puesto[$i][3] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$auxpuesto[$i][4]=number_format ( $puesto[$i][4] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
		$auxpuesto[$i][5]=number_format ( $puesto[$i][7] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	}
}
*/
if($n==4)
{
	echo '<table style="font-family:arial" border="1">';
	$str = '<tr style="background:gray;color:orange;"><th colspan="6">Campeonato de invierno</th></tr>';
	echo ($str);
	$str = '<tr style="background:gray;color:orange;"><th rowspan="2">Puesto</th><th rowspan="2">Participante</th><th rowspan="2">Puntos</th><th colspan="3">Trinque</th>';
	echo ($str);
	$str='<tr style="background:gray;color:orange;"><th>Ordin.</th><th>Por Loca</th><th>SUMA</th></tr>';
	echo ($str);
}
else
{
	echo '<table style="font-family:arial" border="1">';
	$str = '<tr style="background:gray;color:orange;"><th colspan="8">Campeonato de invierno</th></tr>';
	echo ($str);
	$str = '<tr style="background:gray;color:orange;"><th rowspan="2">Puesto</th><th rowspan="2">Participante</th><th rowspan="2">Puntos</th><th rowspan="2">Puntos Com</th><th rowspan="2">TOTAL</th><th colspan="3">Trinque</th>';
	echo ($str);
	$str='<tr style="background:gray;color:orange;"><th>Ordin.</th><th>Por Común</th><th>SUMA</th></tr>';
	echo ($str);
}

if($n==8)
{
	$str = '<tr><td style="background:gray;color:blue;font-weight:bold">Primer Clasificado</td><td style="color:blue">'.$puesto[0][0].'</td><td style="color:blue">'.$puesto[0][1].'</td><td style="color:blue">'.$puesto[0][5].'</td><td style="color:blue">'.$puesto[0][6].'</td><td style="color:blue">'.$puesto[0][2].'</td><td style="color:blue">'.$puesto[0][3].'</td><td style="color:blue">'.$puesto[0][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Segundo Clasificado</td><td>'.$puesto[1][0].'</td><td>'.$puesto[1][1].'</td><td>'.$puesto[1][5].'</td><td>'.$puesto[1][6].'</td><td>'.$puesto[1][2].'</td><td>'.$puesto[1][3].'</td><td>'.$puesto[1][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Tercer Clasificado</td><td>'.$puesto[2][0].'</td><td>'.$puesto[2][1].'</td><td>'.$puesto[2][5].'</td><td>'.$puesto[2][6].'</td><td>'.$puesto[2][2].'</td><td>'.$puesto[2][3].'</td><td>'.$puesto[2][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Cuarto Clasificado</td><td>'.$puesto[3][0].'</td><td>'.$puesto[3][1].'</td><td>'.$puesto[3][5].'</td><td>'.$puesto[3][6].'</td><td>'.$puesto[3][2].'</td><td>'.$puesto[3][3].'</td><td>'.$puesto[3][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Quinto Clasificado</td><td>'.$puesto[4][0].'</td><td>'.$puesto[4][1].'</td><td>'.$puesto[4][5].'</td><td>'.$puesto[4][6].'</td><td>'.$puesto[4][2].'</td><td>'.$puesto[4][3].'</td><td>'.$puesto[4][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Sexto Clasificado</td><td>'.$puesto[5][0].'</td><td>'.$puesto[5][1].'</td><td>'.$puesto[5][5].'</td><td>'.$puesto[5][6].'</td><td>'.$puesto[5][2].'</td><td>'.$puesto[5][3].'</td><td>'.$puesto[5][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Séptimo Clasificado</td><td>'.$puesto[6][0].'</td><td>'.$puesto[6][1].'</td><td>'.$puesto[6][5].'</td><td>'.$puesto[6][6].'</td><td>'.$puesto[6][2].'</td><td>'.$puesto[6][3].'</td><td>'.$puesto[6][4].'</td></tr>
			<tr><td style="background:gray;color:red;font-weight:bold">Farolillo Rojo</td><td style="color:red">'.$puesto[7][0].'</td><td style="color:red">'.$puesto[7][1].'</td><td style="color:red">'.$puesto[7][5].'</td><td style="color:red">'.$puesto[7][6].'</td><td style="color:red">'.$puesto[7][2].'</td><td style="color:red">'.$puesto[7][3].'</td><td style="color:red">'.$puesto[7][4].'</td></tr>';
	echo ($str);
}
else
{
	$str = '<tr><td style="background:gray;color:blue;font-weight:bold">Primer Clasificado</td><td style="color:blue">'.$puesto[0][0].'</td><td style="color:blue">'.$puesto[0][1].'</td><td style="color:blue">'.$puesto[0][2].'</td><td style="color:blue">'.$puesto[0][3].'</td><td style="color:blue">'.$puesto[0][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Segundo Clasificado</td><td>'.$puesto[1][0].'</td><td>'.$puesto[1][1].'</td><td>'.$puesto[1][2].'</td><td>'.$puesto[1][3].'</td><td>'.$puesto[1][4].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Tercer Clasificado</td><td>'.$puesto[2][0].'</td><td>'.$puesto[2][1].'</td><td>'.$puesto[2][2].'</td><td>'.$puesto[2][3].'</td><td>'.$puesto[2][4].'</td></tr>
			<tr><td style="background:gray;color:red;font-weight:bold">Farolillo Rojo</td><td style="color:red">'.$puesto[3][0].'</td><td style="color:red">'.$puesto[3][1].'</td><td style="color:red">'.$puesto[3][2].'</td><td style="color:red">'.$puesto[3][3].'</td><td style="color:red">'.$puesto[3][4].'</td></tr>';
	echo ($str);
}
	
$totalpasta=number_format ( $totalpasta , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$str='<tr style="color:green;"><td style="background:gray;font-weight:bold">Recaudación total:</td><td colspan="5">'.$totalpasta.'.-Euros</td></tr></table><br />';
echo ($str);

if($n==8 && $ano<2024) //Tabla calsificación estilo antiguo, solo para Quiniela Mina y si el año es anterior a 2024
{
	for ($i=0;$i<8;$i++)
	{
		$sumaaciertos=$totalaciertospvea[$i];		
	
		for($iter=1;$iter<20;$iter++)
		{
			$jornadasv=$iter+19;
			$sel=$con->query("SELECT * FROM Computa WHERE Jornada='".$jornadasv."'");
			$computasino = $sel->fetch_assoc();
			$sel=$con->query("SELECT * FROM AciertosAnt WHERE Ano='".$ano."' AND Usuario='".$usuario[$i]."' AND Jornada='".$jornadasv."'");
			$aciertos = $sel->fetch_assoc();
			if($computasino["ComputaMina"]==1)
				$sumaaciertos+=$aciertos["AciertosAnt"];
		}
		$puestoant[$i][0]=$cartelito[$i];
		$puestoant[$i][1]=$sumaaciertos;
	
		//Burbuja de puestos
		if($i>0)
		{
			for($j=$i;$j>0;$j--)
			{
				if(($puestoant[$j][1]>$puestoant[$j-1][1]))
				{
					$puestoantaux[0]=$puestoant[$j-1][0];
					$puestoantaux[1]=$puestoant[$j-1][1];
					
					$puestoant[$j-1][0]=$puestoant[$j][0];
					$puestoant[$j-1][1]=$puestoant[$j][1];
					
					$puestoant[$j][0]=$puestoantaux[0];
					$puestoant[$j][1]=$puestoantaux[1];
				}
			}
		}
	}

	echo '<table style="font-family:arial" border="1">';
	$str = '<tr style="background:gray;color:orange;"><th colspan="3">Campeonato absoluto AL ESTILO ANTIGUO</th></tr>';
	echo ($str);
	$str = '<tr style="background:gray;color:orange;"><th>Puesto</th><th>Participante</th><th>Puntos</th></tr>';
	echo ($str);

	$str = '<tr><td style="background:gray;color:blue;font-weight:bold">Primer Clasificado</td><td style="color:blue">'.$puestoant[0][0].'</td><td style="color:blue">'.$puestoant[0][1].'</td></tr>
		    <tr><td style="background:gray;color:orange;font-weight:bold">Segundo Clasificado</td><td>'.$puestoant[1][0].'</td><td>'.$puestoant[1][1].'</td></tr>
		    <tr><td style="background:gray;color:orange;font-weight:bold">Tercer Clasificado</td><td>'.$puestoant[2][0].'</td><td>'.$puestoant[2][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Cuarto Clasificado</td><td>'.$puestoant[3][0].'</td><td>'.$puestoant[3][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Quinto Clasificado</td><td>'.$puestoant[4][0].'</td><td>'.$puestoant[4][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Sexto Clasificado</td><td>'.$puestoant[5][0].'</td><td>'.$puestoant[5][1].'</td></tr>
			<tr><td style="background:gray;color:orange;font-weight:bold">Séptimo Clasificado</td><td>'.$puestoant[6][0].'</td><td>'.$puestoant[6][1].'</td></tr>
			<tr><td style="background:gray;color:red;font-weight:bold">Farolillo Rojo</td><td style="color:red">'.$puestoant[7][0].'</td><td style="color:red">'.$puestoant[7][1].'</td></tr></table></br>';
	echo ($str);
}

//******************************************************** TABLA RECAUDACION Y DISPOSICION *******************************************************************

echo '<table style="font-family:arial" border="1">';
$str = '<tr style="background:gray;color:orange;"><th colspan="6">Recaudación y disposición</th></tr>';
echo ($str);
$str = '<tr style="background:gray;color:orange;"><th>Participante</th><th>Premios</th><th>Dispuesto</th><th>Por disponer</th></tr>';
echo ($str);

//Burbuja de recaudación
for($i=1;$i<$n;$i++)
{
	for($j=$i;$j>0;$j--)
	{
		if(($puesto[$j][4]>$puesto[$j-1][4]))
		{
			for($k=0;$k<6;$k++) //6 porque 6 son los miembros del array $puesto
			{
				$puestoaux[$k]=$puesto[($j-1)][$k];
				$puesto[($j-1)][$k]=$puesto[$j][$k];
				$puesto[$j][$k]=$puestoaux[$k];
			}
		}
	}
}

for($i=0;$i<$n;$i++)
{
	$pordisponer=$puesto[$i][4]-$puesto[$i][7];
	if($pordisponer<0.01)
		$pordisponer=0; //Para que no salga -0.0, que a veces sale.
	$auxpremios=number_format ( $puesto[$i][4] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$auxdispuesto=number_format ( $puesto[$i][7] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$auxpordisponer=number_format ( $pordisponer , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
	$str = '<tr><td style="background:gray;color:orange;font-weight:bold">'.$puesto[$i][0].'</td>
			<td>'.$auxpremios.'</td>
			<td>'.$auxdispuesto.'</td>
			<td>'.$auxpordisponer.'</td></tr>';
	echo $str;
}
echo '</table>';
?>
