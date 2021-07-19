<?php
session_start();
include('data.php');

function conectarTrafico(){
     $connTrafico = mysql_connect('trafico.masterbus.net', 'masterbus', 'master,07a');
     mysql_select_db('trafico', $connTrafico);
     $query = mysql_query("SELECT *
                           FROM usuarios
                           WHERE id_usuario = '$_POST[txtNombre]' and password = '$_POST[txtPassword]'", $connTrafico) or die(mysql_error());
    // die($query);
     if(mysql_num_rows($query) != 0) {
             $data = mysql_fetch_array($query);
             if ($data['activo']){
	            $_SESSION['auth'] = true;
	            $_SESSION['nivel'] = $data["nivel"];
	            $_SESSION['apenom'] = $data["apenom"];
	            $_SESSION['id_usuario'] = $data["id_usuario"];
	            $_SESSION['todas'] = '1,0';

                mysql_query("insert into log_usuarios (usuario, hinicio, hfin, host)
                                               values ('$data[id_usuario]', now(), now(), '$_SERVER[REMOTE_ADDR]')", $connTrafico);
                print '<SCRIPT language="JavaScript">
                               window.location="http://trafico.masterbus.net/ppal.php?cacc=\'cn\'";
                       </SCRIPT>';
             }
             else{
               echo "<script>window.location='index.php?e=0'</script>";
                  }
     }else {
        echo "<script>window.location='index.php?e=1'</script>";
     }
     
     if(isset($connTrafico)){
	                      mysql_close($connTrafico) or die(alerta(mysql_error()));
     }
}


function accederDiagrama(){
$conexion = conectar_mysql() or die(alerta(mysql_error()));
$query = mysql_query("SELECT *, Concat(Upper(apellido),', ', Upper(nombre)) as name FROM empleados WHERE (legajo = '$_POST[txtNombre]') and (activo = 1)") or die(alerta(mysql_error()));

if(mysql_num_rows($query) != 0) { //Verifica que el chofer no se encuentre deshabilitado

	$data = mysql_fetch_array($query);
    $_SESSION['id_chofer'] = $data["id_empleado"];
    $_SESSION['chofer'] = $data["name"];
	$query = mysql_query("SELECT * FROM passwords WHERE id_chofer = $data[id_empleado]") or die(mysql_error());

	if(mysql_num_rows($query) != 0){  //Verifica que si el chofer esta habilitado, se encuentre generada su clave de acceso caso contrario redirige a la pagina de cambio de contraseña
          $data = mysql_fetch_array($query);

          if (md5($data['clave']) == md5($_POST['txtPassword'])){ //si esta generada la clave de acceso verifica que coincida con la introducida en el formulario de index.php
	               $_SESSION['auth'] = true;
                   $_SESSION['legajo'] = $data["legajo"];
                   $_SESSION['passwd'] = $_POST['txtPassword'];
                   $_SESSION['admin']= mysql_num_rows(mysql_query("SELECT * FROM admin_choferes WHERE id_chofer= $_SESSION[id_chofer]"));
                   $query = mysql_query("UPDATE passwords set ultimoacceso = NOW() WHERE id_chofer = $_SESSION[id_chofer]") or die(mysql_error());
                   echo "<script>window.location='ppal.php'</script>";
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
}
//$connRRHH = conectar_mysql() or die(alerta(mysql_error())); //primero se conecta a RRHH para ver si es un numero de legajo lo que se ingreso como usuario
//$porLegajo="SELECT * FROM empleados WHERE (legajo = '$_POST[txtNombre]')";
if ( is_numeric($_POST['txtNombre'])){

//mysql_num_rows(mysql_query($porLegajo, $connRRHH)) > 0){
   //mysql_close($connRRHH);
   accederDiagrama();
}
else{
     conectarTrafico();
}


?>
