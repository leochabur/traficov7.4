<?php
session_start();
	$SQL_SERVER="localhost";	// direccion del MySQL
	$SQL_USER="root";		// usuario de la db
	$SQL_PASS="master9031";		// password.
	$SQL_DATABASE="rrhh";		// db a usar.
	session_start();
	
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<style type="text/css">

#hintbox{ /*CSS for pop up hint box */
position:absolute;
top: 0;
background-color: lightyellow;
width: 150px; /*Default width of hint.*/
padding: 3px;
border:1px solid black;
font:normal 11px Verdana;
line-height:18px;
z-index:100;
border-right: 3px solid black;
border-bottom: 3px solid black;
visibility: hidden;
}

.hintanchor{ /*CSS for link that shows hint onmouseover*/
font-weight: bold;
color: navy;
margin: 3px 8px;
}

</style>

<script type="text/javascript">

/***********************************************
* Show Hint script- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for this script and 100s more.
***********************************************/

var horizontal_offset="9px" //horizontal offset of hint box from anchor link

/////No further editting needed

var vertical_offset="0" //horizontal offset of hint box from anchor link. No need to change.
var ie=document.all
var ns6=document.getElementById&&!document.all

function getposOffset(what, offsettype){
var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
var parentEl=what.offsetParent;
while (parentEl!=null){
totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
}

function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
var edgeoffset=(whichedge=="rightedge")? parseInt(horizontal_offset)*-1 : parseInt(vertical_offset)*-1
if (whichedge=="rightedge"){
var windowedge=ie && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-30 : window.pageXOffset+window.innerWidth-40
dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure+obj.offsetWidth+parseInt(horizontal_offset)
}
else{
var windowedge=ie && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure-obj.offsetHeight
}
return edgeoffset
}

function showhint(menucontents, obj, e, tipwidth){
if ((ie||ns6) && document.getElementById("hintbox")){
dropmenuobj=document.getElementById("hintbox")
dropmenuobj.innerHTML=menucontents
dropmenuobj.style.left=dropmenuobj.style.top=-500
if (tipwidth!=""){
dropmenuobj.widthobj=dropmenuobj.style
dropmenuobj.widthobj.width=tipwidth
}
dropmenuobj.x=getposOffset(obj, "left")
dropmenuobj.y=getposOffset(obj, "top")
dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+obj.offsetWidth+"px"
dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+"px"
dropmenuobj.style.visibility="visible"
obj.onmouseout=hidetip
}
}

function hidetip(e){
dropmenuobj.style.visibility="hidden"
dropmenuobj.style.left="-500px"
}

function createhintbox(){
var divblock=document.createElement("div")
divblock.setAttribute("id", "hintbox")
document.body.appendChild(divblock)
}

if (window.addEventListener)
window.addEventListener("load", createhintbox, false)
else if (window.attachEvent)
window.attachEvent("onload", createhintbox)
else if (document.getElementById)
window.onload=createhintbox

</script>

<?php titulo($_SESSION["chofer"]);?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./estilo.css" rel="stylesheet" type="text/css">

	<link rel="stylesheet" type="text/css" href="./superfish/css/superfish.css" media="screen">
                <script type="text/javascript" src="./superfish/js/jquery-1.2.6.min.js"></script>
                <script type="text/javascript" src="./superfish/js/hoverIntent.js"></script>
                <script type="text/javascript" src="./superfish/js/superfish.js"></script>
                <script type="text/javascript" src="./superfish/js/supersubs.js"></script>
		<script src="CalendarPopup.js" type="text/javascript"></script>

                <script type="text/javascript">
var cal1= new CalendarPopup();
cal1.setReturnFunction("setMultipleValues1");
cal1.setMonthNames("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
cal1.setMonthAbbreviations("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
cal1.setDayHeaders("D","L","M","M","J","V","S");
cal1.setTodayText("Hoy");
function setMultipleValues1(y,m,d) {
         document.form1.dia1.value=d;
             document.form1.mes1.value=m;
             document.form1.ano1.value=y;
        }

var cal2= new CalendarPopup();
cal2.setReturnFunction("setMultipleValues2");
cal2.setMonthNames("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
cal2.setMonthAbbreviations("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
cal2.setDayHeaders("D","L","M","M","J","V","S");
cal2.setTodayText("Hoy");
function setMultipleValues2(y,m,d) {
         document.form1.dia2.value=d;
             document.form1.mes2.value=m;
             document.form1.ano2.value=y;
        }

                </script>


                <script type="text/javascript">

    $(document).ready(function(){ 
        $("ul.sf-menu").supersubs({ 
            minWidth:    12,   // minimum width of sub-menus in em units 
            maxWidth:    27,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
        }).superfish();  // call supersubs first, then superfish, so that subs are 
                         // not display:none when measuring. Call before initialising 
                         // containing tabs for same reason. 
    }); 
                </script>

                
                
<?

	function password($pass)	//devuelve $pass cifrado usando la funcion password() del mysql
	{
		$miconexion = conectar_mysql();
		$resultado=mysql_query("select password('".$pass."') as p;",$miconexion) or die(mysql_error());
		$datos_sql=mysql_fetch_array($resultado);
		return $datos_sql['p'];
	}
	
	function conectar_mysql()	//conecta usando los datos globales
	{
		global $SQL_SERVER, $SQL_USER, $SQL_PASS,$SQL_DATABASE;
		
		$miconexion = mysql_connect($SQL_SERVER, $SQL_USER, $SQL_PASS) or die(alerta(mysql_error()));
		mysql_select_db($SQL_DATABASE) or die(alerta(mysql_error()));
		return $miconexion;
		
	}
	function conectar_mysqli()	//conecta usando los datos globales
	{
		global $SQL_SERVER, $SQL_USER, $SQL_PASS,$SQL_DATABASE;
		
		$miconexion = new mysqli( $SQL_SERVER, $SQL_USER, $SQL_PASS, $SQL_DATABASE);
		return $miconexion;
		
	}	
	function aviso($mensaje)
	{
		echo("<p align=\"center\" class=\"aviso\" id=\"aviso\"> $mensaje </p>");
		
	}
	
	function alerta($mensaje)
	{
		echo("<p align=\"center\" class=\"alerta\" id=\"alerta\"> $mensaje </p>");
	}
	function piepagina($nivel)
	{
		print "	<p class=\"tmediano\" id=\"ppal\"><span class=\"celeste\">&gt;</span> <a href=\"ppal.php\">Ir al menú Principal</a></p>";
				
        
        print"<p class=\"tmediano\" id=\"salir\"><span class=\"celeste\">&gt;</span> <a href=\"logout.php\">Salir del sistema</a></p>";
		

	}

	function encabezado($nombre,$nivel)
	{
	//print"<table width=\"100%\"  border=\"0\" cellspacing=\"2\" id=\"encabezado\">
  	//		<tr class=\"current\">
    //		<td></td>
    //		<td></td>
  	//		</tr>
	//	</table>";
	?>
	<ul class="sf-menu">
	
		<li class="current"><a href="ppal.php">Principal</a></li>
            <li class="current"><a href="empleado.php">Mi Informacion</a>
            <?php
                 if ($_SESSION['admin'] == 0){
                    echo '<li class="current"><a href="ordenes.php">Ver Diagrama</a>';
                 }
                 else{
                      echo '<li class="current"><a href="diagramas.php">Ver Diagramas de conductores</a>';
                 }
            ?>

       		<li class="current"><a href="chcontrasenia.php">Cambiar mi Contrase&ntilde;a</a>
       		<li class="current"><a href="logout.php">Salir</a>
       
       </ul>


	<p>
	&nbsp;
	<br>
	<?
	}

	function titulo($apenom)
	{
		print"<title>Sistema de Consulta de Diagrama v1.1 - MasterBus - Bienvenido: $apenom</title>";

	}
	
//////////////////////SERVICIOS LINEA/////////////
	function piepagina_sl($nivel)
	{
		/*if(isset($miconexion)){
			mysql_close($miconexion) or die(alerta(mysql_error()));
			alerta("cerrado, OK");
		}
		else alerta("no existe miconexion");*/
		print "	<p class=\"tmediano\" id=\"ppal\"><span class=\"celeste\">&gt;</span> <a href=\"/ppal.php\">Ir al menú Principal</a></p>
				<p class=\"tmediano\" id=\"salir\"><span class=\"celeste\">&gt;</span> <a href=\"/logout.php\">Salir del sistema</a></p>
				<p align=\"center\" id=\"menu2\"><a href=\"/ppal.php\">&lt;menú principal&gt;</a><a href=\"/logout.php\">&lt;salir&gt;</a></p>
				<p class=\"mensaje\" id=\"fecha\">Servicios de línea - &uacute;ltima revisi&oacute;n: 06/02/2006</p>";																

        $link= conectar_mysql();
        $query="SELECT licencia, date_format(MAX(vigencia_hasta),'%d/%m/%y') as fecha, DATEDIFF(vigencia_hasta, CURDATE()) as dias
                FROM licenciaconductor lc
                LEFT JOIN licencias l ON l.id = lc.id_licencia
                WHERE (lc.id_conductor = $_SESSION[id_chofer])
                GROUP BY lc.id_licencia";
        alerta($query);
        $result=mysql_query($query, $link);
        if (mysql_num_rows($result) > 0){
           while ($data= mysql_fetch_array($result)){
                 if ($data['dias'] <= 0){
                    alerta("Su licencia $data[licencia] venció el $data[fecha]");
                 }
                 else{
                      if ($data['dias'] <= 20){
                         alerta("Su licencia $data[licencia] vence dentro de $data[dias]. El dia $data[fecha]");
                      }
                 }
           }
        }
    }
	function encabezado_sl($nombre,$nivel)
	{
	print"<table width=\"100%\"  border=\"0\" cellspacing=\"2\" id=\"encabezado\">
  			<tr class=\"mensaje\">
    		<td>Bienvenido $nombre </td>
    		<td>Nivel: $nivel</td>
  			</tr>
		</table>
		<p align=\"center\" id=\"menu3\"><a href=\"/ppal.php\">&lt;menú principal&gt;</a><a href=\"/logout.php\">&lt;salir&gt;</a></p>";
	}
	function titulo_sl($apenom)
	{
		print"<title>Servicios de línea - MasterBus - Bienvenido: $apenom</title>";

	}
	
	function fechaAseg($fecha,$hora)
	{//FORMATO aaaa-mm-dd hh:mm:ss
		sscanf($fecha,"%d-%d-%d",$ano,$mes,$dia);
		sscanf($hora,"%d:%d:%d",$hora,$min,$seg);
		$segundos=mktime($hora,$min,$seg,$mes,$dia,$ano);
		return $segundos;
	}
	
	function sumaHoras($hora1,$signo,$hora2)
	{//FORMATO hh:mm:ss $signo="+" ó $signo="-"
		sscanf($hora1,"%d:%d:%d",$h1,$m1,$s1);
		sscanf($hora2,"%d:%d:%d",$h2,$m2,$s2);
		$t0=mktime(0,0,0,8,9,1983);//JE JE LINDA FECHA NO?
		$t1=mktime($h1,$m1,$s1,8,9,1983);
		$t2=mktime($h2,$m2,$s2,8,9,1983);
		if($signo=="+") $suma=$t1+$t2-2*$t0;
			else $suma=$t1-$t2;
		$aux=$suma/3600;
		$tiempo=substr($aux,0,10);
		sscanf($tiempo,"%d.%d",$h,$xx);
		$resto=fmod($tiempo,1);
		$resto=round($resto*60);
		if($resto<0)
			$resto=substr($resto,1);
		if($resto<10){
				settype($resto,"string");
				$resto="0".$resto;
		}
		if($h<10&&$h>=00){
				settype($h,"string");
				$h="0".$h;
		}
		if($h<00&&$h>-10){
				settype($h,"string");
				$h=substr($h,1);
				$h="-0".$h;
		}
		$resultado=$h.":".$resto;//DEVUELVE EN FORMATO hh:mm
		return $resultado;
	}

function mes($m)
{
	switch($m){
		case 1;
			$mes="Enero";
			break;
		case 2;
			$mes="Febrero";
			break;	
		case 3;
			$mes="Marzo";
			break;
		case 4;
			$mes="Abril";
			break;			
		case 5;
			$mes="Mayo";
			break;
		case 6;
			$mes="Junio";
			break;
		case 7;
			$mes1="Julio";
			break;
		case 8;
			$mes="Agosto";
			break;
		case 9;
			$mes="Septiembre";
			break;
		case 10;
			$mes="Octubre";
			break;
		case 11;
			$mes="Noviembre";
			break;
		case 12;
			$mes="Diciembre";
			break;
		}
return $mes;
}

function array_envia($array) 
{ 
    $tmp = serialize($array); 
    $tmp = urlencode($tmp); 
    return $tmp; 
} 

function array_recibe($url_array) 
{ 
    $tmp = stripslashes($url_array); 
    $tmp = urldecode($tmp); 
    $tmp = unserialize($tmp); 
	return $tmp; 
} 

function codBarras($msj)
{
	$msj="*".strtoupper($msj)."*";
	print"<table width=\"10%\" bgcolor=\"#FFFFFF\"><tr><td class=\"barras\" align=\"center\">$msj<br><span class=\"diminuto\">$msj</span></td></tr>    </table>";
}
function alertPop2($mensaje){
	print"	<script language=\"javascript\" type=\"text/javascript\">
			alert(\"$mensaje\");
			</script>";
}

function cantDias($m,$a) 
{
	$dias= array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	if($a%4 == 0 && $a%100 != 0 || $a%400 == 0) $dias[2]=29; 
	return $dias[$m];
}		


?>

