<?php
session_start();
include('../controlador/bdadmin.php');
$conn = conexcion(true);
$sql = "SELECT id_empleado, Concat(Upper(apellido),', ', Upper(nombre)) as name, upper(razon_social) as emple
                      FROM empleados e
                      INNER JOIN empleadores emp ON emp.id = e.id_empleador
                      WHERE (legajo = '$_POST[txtNombre]') and (emp.cuit_cuil = '$_POST[cuit]') and (e.activo = 1)";

$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if(mysqli_num_rows($query) != 0) { //Verifica que el chofer no se encuentre deshabilitado

	$data = mysqli_fetch_array($query, MYSQL_ASSOC);
  mysqli_free_result($query);
  $_SESSION['id_chofer'] = $data["id_empleado"];
  $_SESSION['chofer'] = $data["name"];
  $_SESSION['empleador'] = $data["emple"];
	$query = mysqli_query($conn, "SELECT * FROM passwords WHERE id_chofer = $data[id_empleado]");

	if($data = mysqli_fetch_array($query, MYSQL_ASSOC))
  {  //Verifica que si el chofer esta habilitado, se encuentre generada su clave de acceso caso contrario redirige a la pagina de cambio de contraseña
         
          if (md5($data['clave']) == md5($_POST['txtPassword']))
          { //si esta generada la clave de acceso verifica que coincida con la introducida en el formulario de index.php
	                 $_SESSION['auth'] = true;
                   $_SESSION['id_chofer'] = $data["id_chofer"];
                   $_SESSION['legajo'] = $data["legajo"];
                   $_SESSION['passwd'] = $_POST['txtPassword'];
                   $_SESSION['super'] = $data["super"];
                   $_SESSION['str'] = $data["estructura"];                   
                   $_SESSION['fleteros'] = 1;//$data["vistafletero"];
                   $_SESSION['admin'] = 0;
                   $query = mysqli_query($conn, "UPDATE passwords set ultimoacceso = NOW() WHERE id_chofer = $_SESSION[id_chofer]");
                   mysqli_free_result($query);
                   mysqli_close($conn);
                   header ("Location: ppal.php?pv=s");
          }
          else{
               //echo 'ok';
               echo "<script>window.location='index.php?e=1'</script>"; //Contraseña incorrecta
          }
  }
  else
  {

          if (md5($_POST['txtNombre']) == md5($_POST['txtPassword'])){    //La clave no ha sido generada, verifica que el usuario y clave originales coincida y redirecciona a cambiar la contraseña
             $_SESSION['auth'] = true;
             $_SESSION['legajo'] = $data["legajo"];
             $_SESSION['passwd'] = $_POST['txtPassword'];
             $_SESSION['super'] = $data['super'];
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

?>
