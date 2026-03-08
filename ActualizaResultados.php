<?php
$signo=$_GET["q"];
$casilla=$_GET["p"];
$existe=0;

function tofloat($num) {
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : 
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
   
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    } 

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
}

error_reporting(E_ALL ^ E_DEPRECATED);

//Se pasan las exises minúsculas a mayúsculas por si se mete en minúscula en la tabla
if($signo=="x") $signo="X";
//Se vigila que el signo de los partidos se 1, X o 2 salvo el pleno y los premios
if($signo!="1" && $signo!="X" && $signo!="2" && $casilla!="R15_1" && $casilla!="R15_2" && $casilla!="P1" && $casilla!="P2" && $casilla!="P3" && $casilla!="P4" && $casilla!="P5" && $casilla!="P6")
	$signo="";
//Se convierte el texto de pasta (con coma o punto, es indiferente) a float 
if($casilla=="P1" || $casilla=="P2" || $casilla=="P3" || $casilla=="P4" || $casilla=="P5" || $casilla=="P6")
	$signo=tofloat($signo);


$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

$sel = $con->query("SELECT * FROM Jornada");
$jornadabd=$sel->fetch_assoc();
$jornada=$jornadabd['Jornadaencurso'];

$sel = $con->query("SELECT * FROM Escrutinio");
while($ultimajornada = $sel->fetch_assoc())
{
	if($ultimajornada['Jornada']==$jornada)
	{
		$existe=1;
		break;
	}
}

if($existe==0)
	$con->query("INSERT INTO Escrutinio(Jornada,R1,R2,R3,R4,R5,R6,R7,R8,R9,R10,R11,R12,R13,R14,R15_1,R15_2,P1,P2,P3,P4,P5,P6) VALUES(".$jornada.",'','','','','','','','','','','','','','','?','?',0,0,0,0,0,0)");

$con->query("UPDATE Escrutinio SET ".$casilla." = '".$signo."' WHERE Jornada = ".$jornada);
echo $signo;

//Procesos para actualizar las tablas de resultados.
$sel = $con->query("SELECT * FROM Ano");
$Ano=$sel->fetch_assoc();
$AnoEnCurso=$Ano["anoencurso"];

$sel = $con->query("SELECT * FROM Escrutinio WHERE Jornada = ".$jornada);
$resultado=$sel->fetch_assoc();

$pastap15=$resultado["P1"];
$pasta14=$resultado["P2"];
$pasta13=$resultado["P3"];
$pasta12=$resultado["P4"];
$pasta11=$resultado["P5"];
$pasta10=$resultado["P6"];

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
	//Quiniela Mina
$Singosdesdobles=array(array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,1,0,1,1,1,0,0,1,1,1,1,1,1,1,1,0,1,0,0,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,1,1),
					   array(0,1,1,1,0,0,1,1,0,0,1,1,1,0,0,0,0,1,1,1,1,1,0,0,0,0,0,1,0,0,0,1,1,1,0,1,1,0,0,0,0,0,0,0,1,1,1,0,0,0,0,1,1,1,1,1,0,0,0,1,1,1,1,1),
					   array(0,0,0,1,0,0,1,0,1,0,1,1,0,0,0,0,0,0,1,1,0,0,0,0,1,0,0,1,1,1,0,1,1,0,0,1,0,0,1,0,1,1,1,1,0,0,0,1,1,1,1,1,1,0,0,0,1,1,1,1,1,0,1,1),
					   array(0,0,0,0,1,0,0,1,1,1,1,1,0,0,1,1,0,1,0,1,1,0,1,1,0,0,0,0,0,0,0,0,0,1,1,1,0,1,1,0,0,1,1,1,0,1,1,0,1,1,0,1,1,0,0,1,0,0,1,0,1,1,1,0),
					   array(1,0,1,0,0,0,1,0,0,1,1,1,0,1,1,0,0,0,1,0,1,1,0,1,1,1,1,1,0,1,0,1,0,0,1,1,1,0,1,0,0,0,0,1,1,1,0,1,1,0,1,1,0,0,0,1,0,0,1,0,0,1,0,0),
					   array(0,0,0,0,0,1,1,1,0,0,1,0,1,1,1,0,0,0,0,0,0,1,1,1,1,0,1,0,0,0,0,1,1,0,0,1,1,1,0,1,1,1,0,1,0,0,1,0,0,1,1,0,1,0,1,1,0,1,1,1,1,1,0,0));
	//Quiniela Manolín
$SingosdesdoblesManolin=array(array(0,0,1,1,1,1,0,0,1,1,0,0,1,1,0,0),
							  array(0,0,1,1,1,1,0,0,0,0,1,1,0,0,1,1),
							  array(0,0,1,1,0,0,1,1,1,1,0,0,0,0,1,1),
							  array(0,0,1,1,0,0,1,1,0,0,1,1,1,1,0,0),
							  array(0,1,0,1,1,0,1,0,0,1,0,1,0,1,0,1),
							  array(0,1,0,1,0,1,0,1,1,0,1,0,0,1,0,1),
							  array(0,1,0,1,0,1,0,1,0,1,0,1,1,0,1,0));

$concursante=array('fermina','michel','julio','javier','ferming','felipe','antonio','alvaro','alfonso','caixero','fermin','manolin','loca');
for($i=0;$i<13;$i++)
{
	$sumaciertos[$i]=0;
	//$sumaciertosspq[$i]=0;
}

//Primero se calcula el número de acertantes de cada partido (Solo para Quiniela Mina, es decir para los primeros 8 concursantes)
$NumeroAcertantes=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
for($i=0;$i<8;$i++)
{
	$nombre=$concursante[$i];
	$sel=$con->query("SELECT * FROM Pronosticos WHERE Usuario = '".$nombre."' AND Jornada = ".$jornada);
	$pronostico=$sel->fetch_assoc();
	for($j=0;$j<14;$j++)
	{
		$b=$j+1;
		switch($j)
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
				$a=$j+1;
		}
		if($resultado["R".$b]=='1' && $pronostico["P".$a."1"]=='checked')
			$NumeroAcertantes[$j]++;
		else if($resultado["R".$b]=='X' && $pronostico["P".$a."X"]=='checked')
			$NumeroAcertantes[$j]++;
		else if($resultado["R".$b]=='2' && $pronostico["P".$a."2"]=='checked')
			$NumeroAcertantes[$j]++;
	}
	
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
		if(($golesr1==$pronostico["PF1"]) && ($golesr2==$pronostico["PF2"]))
			$NumeroAcertantes[14]++;
	}
	
	$sumaciertosANT[$i]=0; //Los aciertos al estilo antiguo para Quiniela Mina
}
//Ahora ya se calcula la puntuación
	//Variables para olvidadizos quiniela Mina
$olvidadizo=array(TRUE,TRUE,TRUE,TRUE,TRUE,TRUE,TRUE,TRUE);
$menorpuntuacion=1000;
$menorpuntuacionANT=1000;
for($i=0;$i<13;$i++)
{
	$nombre=$concursante[$i];
	$sel=$con->query("SELECT * FROM Pronosticos WHERE Usuario = '".$nombre."' AND Jornada = ".$jornada);
	$pronostico=$sel->fetch_assoc();
	for($j=0;$j<14;$j++)
	{
		$b=$j+1;
		switch($j)
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
				$a=$j+1;
		}
		if($i<8) //Caso Quiniela Mina, para meter los puntos en función de los acertantes de ese partido
		{
			if($resultado["R".$b]=='1' && $pronostico["P".$a."1"]=='checked')
			{
				$sumaciertosANT[$i]++;
				//$sumaciertos[$i]+=8/$NumeroAcertantes[$j];
				$sumaciertos[$i]++;
				$olvidadizo[$i]=FALSE;
			}
			else if($resultado["R".$b]=='X' && $pronostico["P".$a."X"]=='checked')
			{
				$sumaciertosANT[$i]++;
				//$sumaciertos[$i]+=8/$NumeroAcertantes[$j];
				$sumaciertos[$i]++;
				$olvidadizo[$i]=FALSE;
			}
			else if($resultado["R".$b]=='2' && $pronostico["P".$a."2"]=='checked')
			{
				$sumaciertosANT[$i]++;
				//$sumaciertos[$i]+=8/$NumeroAcertantes[$j];
				$sumaciertos[$i]++;
				$olvidadizo[$i]=FALSE;
			}
		}
		else
		{			
			if($resultado["R".$b]=='1' && $pronostico["P".$a."1"]=='checked')
				$sumaciertos[$i]++;
			else if($resultado["R".$b]=='X' && $pronostico["P".$a."X"]=='checked')
				$sumaciertos[$i]++;
			else if($resultado["R".$b]=='2' && $pronostico["P".$a."2"]=='checked')
				$sumaciertos[$i]++;
		}
	}
	
	//Pleno al quince y trinque
	if($nombre=='alfonso' || $nombre=='caixero' || $nombre=='fermin' || $nombre=='manolin' || $nombre=='loca') //Caso Quniela Manolín
	{
		//Aciertos pleno al 15
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
			$sumaciertos[$i]++;
			if($golesp1==$golesr1 || ($pronostico["PF1"]=='M' && $golesr1>2))
				$sumaciertos[$i]++;
			if($golesp2==$golesr2 || ($pronostico["PF2"]=='M' && $golesr2>2))
				$sumaciertos[$i]++;
		}
		
		//Trinque
		$numerodedoble=0;
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
				
			$casillasmarcadas=0;
			$marcadosencillo='0';
			$marcadodoble[0]='0';
			$marcadodoble[1]='0';
			if($pronostico["P".$a."1"]=='checked')
			{
				$casillasmarcadas++;
				$marcadosencillo='1';
				$marcadodoble[0]='1';
			}
			if($pronostico["P".$a."X"]=='checked')
			{
				$casillasmarcadas++;
				$marcadosencillo='X';
				if($marcadodoble[0]=='1')
					$marcadodoble[1]='X';
				else
					$marcadodoble[0]='X';
			}
			if($pronostico["P".$a."2"]=='checked')
			{
				$casillasmarcadas++;
				$marcadosencillo='2';
				$marcadodoble[1]='2';
			}
			
			if($casillasmarcadas==2)
			{
				for($k=0;$k<16;$k++)
				{
					$signodesarrollo[$k]=$marcadodoble[$SingosdesdoblesManolin[$numerodedoble][$k]];
					if($resultado["R".$j]==$signodesarrollo[$k])
						$aciertosdesarrollo[$k]++;
				}
				$numerodedoble++;
			}
			else
			{
				if($resultado["R".$j]==$marcadosencillo)
				{
					for($k=0;$k<16;$k++)
						$aciertosdesarrollo[$k]++;
				}
			}
		}
			
		$trinque=0;
		for($k=0;$k<16;$k++) //Se miran las 16 columnas del desarrollo
		{
			if($aciertosdesarrollo[$k]==14)
			{
				$trinque+=$pasta14;
				if($sumaciertos[$i]==17) //Solo si sumaciertos es 17 se acertó también el pleno al 15
					$trinque+=$pastap15;
			}
			else if($aciertosdesarrollo[$k]==13)
				$trinque+=$pasta13;
			else if($aciertosdesarrollo[$k]==12)
				$trinque+=$pasta12;
			else if($aciertosdesarrollo[$k]==11)
				$trinque+=$pasta11;
			else if($aciertosdesarrollo[$k]==10)
				$trinque+=$pasta10;
		}
			
		$existe=0;
		$sel=$con->query("SELECT * FROM Resultados WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada);
		while($resultadosenbd=$sel->fetch_assoc())
		{
			if($resultadosenbd["Usuario"]==$nombre)
				$existe=1;
		}
		if($existe==0)
		{
			$con->query("INSERT INTO Resultados(Ano,Jornada,Usuario,Aciertos,Premios,Restas,Sumas,Loca) VALUES(".$AnoEnCurso.",".$jornada.",'".$nombre."',0,0,0,0,0)");
		}
		$con->query("UPDATE Resultados SET `Premios`=".$trinque." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$nombre."'");
		//Se imputa lo correspondiente a la loca al usuario loco.
		if($nombre=='loca')
		{
			$restodediv=$jornada % 4;
			switch ($restodediv)
			{
				case 1:
					$elloco="alfonso";
					break;
				case 2:
					$elloco="caixero";
					break;
				case 3:
					$elloco="fermin";
					break;
				case 0:
					$elloco="manolin";
					break;
			}
			$con->query("UPDATE Resultados SET `Loca`=".$trinque." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$elloco."'");
		}
	}
	else //caso Quiniela Mina
	{
		$trinque=0;
		if($olvidadizo[$i]==FALSE)
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
				if(($golesr1==$pronostico["PF1"]) && ($golesr2==$pronostico["PF2"]))
				{
					$sumaciertosANT[$i]++;
					//$sumaciertos[$i]+=8/$NumeroAcertantes[14];
					$sumaciertos[$i]++;
				}
			}
			
			//Trinque
			$numerodedoble=0;
			for($j=0;$j<2;$j++)
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
					
				$marcado[0]='0';
				$marcado[1]='0';
				if($pronostico["P".$a."1"]=='checked')
				{
					$marcado[0]='1';
					$marcado[1]='1';
				}
				if($pronostico["P".$a."X"]=='checked')
				{
					$marcado[1]='X';
					if($marcado[0]=='0')
						$marcado[0]='X';	
				}
				if($pronostico["P".$a."2"]=='checked')
				{
					$marcado[1]='2';
					if($marcado[0]=='0')
						$marcado[0]='2';
				}
				
				if($resultado["R".$j]==$marcado[0])
					$aciertosdesarrollo[0]++;
				if($resultado["R".$j]==$marcado[1])
					$aciertosdesarrollo[1]++;
			}
			for($k=0;$k<2;$k++) //Se miran las 2 columnas del desarrollo
			{
				if($aciertosdesarrollo[$k]==14)
				{
					$trinque+=$pasta14;
					if($sumaciertos[$i]==15) //Solo si sumaciertos es 15 se acertó también el pleno al 15
						$trinque+=$pastap15;
				}
				else if($aciertosdesarrollo[$k]==13)
					$trinque+=$pasta13;
				else if($aciertosdesarrollo[$k]==12)
					$trinque+=$pasta12;
				else if($aciertosdesarrollo[$k]==11)
					$trinque+=$pasta11;
				else if($aciertosdesarrollo[$k]==10)
					$trinque+=$pasta10;
			}
			
			if($sumaciertosANT[$i]<$menorpuntuacionANT) //Menor puntuación tabla antigua, para olvidadizos
				$menorpuntuacionANT=$sumaciertosANT[$i];
			if($sumaciertos[$i]<$menorpuntuacion) //Menor puntuación tabla nueva, para olvidadizos;
				$menorpuntuacion=$sumaciertos[$i];
		}
		$existe=0;
		$sel=$con->query("SELECT * FROM Resultados WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada);
		while($resultadosenbd=$sel->fetch_assoc())
		{
			if($resultadosenbd["Usuario"]==$nombre)
				$existe=1;
		}
		if($existe==0)
		{
			$con->query("INSERT INTO Resultados(Ano,Jornada,Usuario,Aciertos,Premios,Restas,Sumas,Loca) VALUES(".$AnoEnCurso.",".$jornada.",'".$nombre."',0,0,0,0,0)");
		}
		$con->query("UPDATE Resultados SET `Premios`=".$trinque." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$nombre."'");

		//La comunitaria
		$sel=$con->query("SELECT * FROM Pronosticoscom WHERE Jornada = ".$jornada);
		$procom=$sel->fetch_assoc();
		for($j=0;$j<64;$j++)
			$aciertosdesarrollo[$j]=0;
		//Los partidos normales
		$numerodetriple=0;
		$numerodedoble=0;
		for($j=1;$j<15;$j++)
		{
			if($j==10)
				$a='A';
			else if($j==11)
				$a='B';
			else if($j==12)
				$a='C';
			else if($j==13)
				$a='D';
			else if($j==14)
				$a='E';
			else
				$a=$j;

			for($k=0;$k<64;$k++)
				$signodesarrollo[$k]="";
			$casillasmarcadas=0;
			$marcadosencillo='0';
			$marcadodoble[0]='0';
			$marcadodoble[1]='0';
			if($procom["P".$a."1C"]=='checked')
			{
				$casillasmarcadas++;
				$marcadosencillo='1';
				$marcadodoble[0]='1';
			}
			if($procom["P".$a."XC"]=='checked')
			{
				$casillasmarcadas++;
				$marcadosencillo='X';
				if($marcadodoble[0]=='1')
					$marcadodoble[1]='X';
				else
					$marcadodoble[0]='X';
			}
			if($procom["P".$a."2C"]=='checked')
			{
				$casillasmarcadas++;
				$marcadosencillo='2';
				$marcadodoble[1]='2';
			}
			if($casillasmarcadas==3)
			{
				$numerodetriple++;
				for($k=0;$k<64;$k++)
				{
					if($numerodetriple==1)
						$signodesarrollo[$k]=$Singosdestriples1[$k];
					else
						$signodesarrollo[$k]=$Singosdestriples2[$k];
					if($resultado["R".$j]==$signodesarrollo[$k])
						$aciertosdesarrollo[$k]++;
				}
			}
			else if($casillasmarcadas==2)
			{
				for($k=0;$k<64;$k++)
				{
					$signodesarrollo[$k]=$marcadodoble[$Singosdesdobles[$numerodedoble][$k]];
					if($resultado["R".$j]==$signodesarrollo[$k])
						$aciertosdesarrollo[$k]++;
				}
				$numerodedoble++;
			}
			else
			{
				for($k=0;$k<64;$k++)
					$signodesarrollo[$k]=$marcadosencillo;
				if($resultado["R".$j]==$marcadosencillo)
				{
					for($k=0;$k<64;$k++)
						$aciertosdesarrollo[$k]++;
				}
			}
		}
		//El pleno al 15
		if($resultado["R15_1"]==$procom["PF1C"] && $resultado["R15_2"]==$procom["PF2C"])
		{
			for($j=0;$j<64;$j++)
			{
				if($aciertosdesarrollo[$j]==14)
					$aciertosdesarrollo[$j]=15;
			}
		}
		$trinque=0;
		for($j=0;$j<64;$j++)
		{
			if($aciertosdesarrollo[$j]==15)
				$trinque+=($resultado["P1"]+$resultado["P2"]); //Si hay pleno al 15 también se cobra una de catorce
			if($aciertosdesarrollo[$j]==14)
				$trinque+=$resultado["P2"];
			if($aciertosdesarrollo[$j]==13)
				$trinque+=$resultado["P3"];
			if($aciertosdesarrollo[$j]==12)
				$trinque+=$resultado["P4"];
			if($aciertosdesarrollo[$j]==11)
				$trinque+=$resultado["P5"];
			if($aciertosdesarrollo[$j]==10)
				$trinque+=$resultado["P6"];
		}
		
		//$elcomun=$procom["Usuario"];
		$restodediv=$jornada % 8;
		switch ($restodediv)
		{
			case 7:
				$elcomun="fermina";
				break;
			case 0:
				$elcomun="michel";
				break;
			case 1:
				$elcomun="julio";
				break;
			case 2:
				$elcomun="javier";
				break;
			case 3:
				$elcomun="ferming";
				break;
			case 4:
				$elcomun="felipe";
				break;
			case 5:
				$elcomun="antonio";
				break;
			case 6:
				$elcomun="alvaro";
				break;
		}
		$con->query("UPDATE Resultados SET `Loca`=".$trinque." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$elcomun."'");
	}
	$con->query("UPDATE Resultados SET `Aciertos`=".$sumaciertos[$i]." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$nombre."'");
	
	//Los aciertos al estilo antiguo, solo para Quiniela Mina
	if($i<8)
	{
		$existe=0;
		$sel=$con->query("SELECT * FROM AciertosAnt WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada);
		while($resultadosenbd=$sel->fetch_assoc())
		{
			if($resultadosenbd["Usuario"]==$nombre)
				$existe=1;
		}
		if($existe==0)
		{
			$con->query("INSERT INTO AciertosAnt(Ano,Jornada,Usuario,AciertosAnt) VALUES(".$AnoEnCurso.",".$jornada.",'".$nombre."',0)");
		}
		$con->query("UPDATE AciertosAnt SET `AciertosAnt`=".$sumaciertosANT[$i]." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$nombre."'");
	}
}

//Update de resultados para los olvidadizos.
for($i=0;$i<8;$i++)
{
	if($olvidadizo[$i]==TRUE)
	{
		$nombre=$concursante[$i];
		
		if($menorpuntuacionANT>0)
			$sumaciertosANT[$i]=$menorpuntuacionANT-1;
		else
			$sumaciertosANT[$i]=0;
		
		if($menorpuntuacion>=1)
			$sumaciertos[$i]=$menorpuntuacion-1;
		else
			$sumaciertos[$i]=0;
		
		$con->query("UPDATE AciertosAnt SET `AciertosAnt`=".$sumaciertosANT[$i]." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$nombre."'");
		$con->query("UPDATE Resultados SET `Aciertos`=".$sumaciertos[$i]." WHERE Ano = ".$AnoEnCurso." AND Jornada = ".$jornada." AND Usuario ='".$nombre."'");
	}
}
?>
