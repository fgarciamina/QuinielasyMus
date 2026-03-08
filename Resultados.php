<?php
error_reporting(E_ALL ^ E_DEPRECATED);

$con = new mysqli("127.0.0.1", "quinielero", "quinielasymus2017", "quiniela_mus");
if ($con->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
}

echo '<table style="font-family:arial" border="1">';
$str = '<tr style="background:gray;color:orange;"><th>Nº</th><th>PARTIDO</th><th>SIGNO RESULTANTE</th>';
echo ($str);

$sel = $con->query("SELECT * FROM Jornada");
$jornada=$sel->fetch_assoc();

$sel = $con->query("SELECT * FROM Escrutinio WHERE Jornada=".$jornada['Jornadaencurso']);
$esc=$sel->fetch_assoc();

$sel = $con->query("SELECT * FROM Partidos WHERE Jornada=".$jornada['Jornadaencurso']);
$partido=$sel->fetch_assoc();

//Partidos normales
$str = '<tr><td style="background:gray;color:orange;font-weight:bold">1</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P1"].'</td><td style="text-align:center"><input type="text" id="R1" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R1"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">2</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P2"].'</td><td style="text-align:center"><input type="text" id="R2" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R2"].'></td></tr>                                        
		<tr><td style="background:gray;color:orange;font-weight:bold">3</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P3"].'</td><td style="text-align:center"><input type="text" id="R3" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R3"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">4</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P4"].'</td><td style="text-align:center"><input type="text" id="R4" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R4"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">5</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P5"].'</td><td style="text-align:center"><input type="text" id="R5" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R5"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">6</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P6"].'</td><td style="text-align:center"><input type="text" id="R6" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R6"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">7</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P7"].'</td><td style="text-align:center"><input type="text" id="R7" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R7"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">8</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P8"].'</td><td style="text-align:center"><input type="text" id="R8" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R8"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">9</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P9"].'</td><td style="text-align:center"><input type="text" id="R9" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R9"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">10</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P10"].'</td><td style="text-align:center"><input type="text" id="R10" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R10"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">11</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P11"].'</td><td style="text-align:center"><input type="text" id="R11" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R11"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">12</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P12"].'</td><td style="text-align:center"><input type="text" id="R12" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R12"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">13</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P13"].'</td><td style="text-align:center"><input type="text" id="R13" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R13"].'></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">14</td><td style="background:gray;color:orange;font-weight:bold">'.$partido["P14"].'</td><td style="text-align:center"><input type="text" id="R14" onkeyup="ActualizaResultados(value,id)" style="width:15px;text-align:center" size="1" maxlength="1" value='.$esc["R14"].'></td></tr></table>';
//echo utf8_encode($str);
echo ($str);

//Pleno al 15
$str = '<table style="font-family:arial" border="1">
        <tr style="color:red">
		<td style="background:gray;font-weight:bold">P-15</td>
		<td style="background:gray;font-weight:bold">'.$partido["P15"].'</td>
		<td><input type="text" size="2" id="R15_1" value="'.$esc["R15_1"].'" onchange="ActualizaResultados(value,id)" style="text-align:center;color:red"></td>
		<td><input type="text" size="2" id="R15_2" value="'.$esc["R15_2"].'" onchange="ActualizaResultados(value,id)" style="text-align:center;color:red"></td></tr></table>';
echo ($str);

$premiop15=number_format ( $esc["P1"] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$premio14=number_format ( $esc["P2"] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$premio13=number_format ( $esc["P3"] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$premio12=number_format ( $esc["P4"] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$premio11=number_format ( $esc["P5"] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );
$premio10=number_format ( $esc["P6"] , $decimals = 2 , $dec_point = ',' , $thousands_sep = '.' );

//La tabla de la pasta
echo '|$|<table style="font-family:arial" border="1">';
$str = '<tr style="background:gray;color:orange"><th></th><th>PREMIO</th></tr>
		<tr style="color:red"><td style="background:gray;font-weight:bold">Pleno al 15:</td><td><input value="'.$premiop15.'" type="text" size="15" maxlength="15" id="P1" style="text-align:right;color:red"  onkeyup="if(event.keyCode == 13) ActualizaResultados(value,id)"></input></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">14 aciertos:</td><td><input value="'.$premio14.'" type="text" size="15" maxlength="15" id="P2" style="text-align:right" onkeyup="if(event.keyCode == 13) ActualizaResultados(value,id)"></input></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">13 aciertos:</td><td><input value="'.$premio13.'" type="text" size="15" maxlength="15" id="P3" style="text-align:right" onkeyup="if(event.keyCode == 13) ActualizaResultados(value,id)"></input></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">12 aciertos:</td><td><input value="'.$premio12.'" type="text" size="15" maxlength="15" id="P4" style="text-align:right" onkeyup="if(event.keyCode == 13) ActualizaResultados(value,id)"></input></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">11 aciertos:</td><td><input value="'.$premio11.'" type="text" size="15" maxlength="15" id="P5" style="text-align:right" onkeyup="if(event.keyCode == 13) ActualizaResultados(value,id)"></input></td></tr>
		<tr><td style="background:gray;color:orange;font-weight:bold">10 aciertos:</td><td><input value="'.$premio10.'" type="text" size="15" maxlength="15" id="P6" style="text-align:right" onkeyup="if(event.keyCode == 13) ActualizaResultados(value,id)"></input></td></tr></table>';
echo $str;
?>
