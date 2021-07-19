<?php
session_start();
include('../controlador/bdadmin.php');
$conn = conexcion(true);
$sql = "SELECT id_empleado, Concat(Upper(apellido),', ', Upper(nombre)) as name, upper(razon_social) as emple, REPLACE(nrodoc,'.','') as dni
        FROM empleados e
        INNER JOIN empleadores emp ON emp.id = e.id_empleador
        WHERE (nrodoc <> '') AND (REPLACE(nrodoc,'.','') = '$_POST[txtNombre]') and (e.activo = 1) and (emp.activo = 1) AND (emp.id not in (1))";



$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if($data = mysqli_fetch_array($query, MYSQL_ASSOC)) { //Verifica que el chofer no se encuentre deshabilitado

  
  $_SESSION['id_chofer'] = $data["id_empleado"];
  $_SESSION['chofer'] = $data["name"];
  $_SESSION['empleador'] = $data["emple"];
	//$query = mysqli_query($conn, "SELECT * FROM passwords WHERE id_chofer = $data[id_empleado]");

	if (md5($data['clave']) == md5($_POST['dni']))
  {  
         
	                 $_SESSION['auth'] = true;
                   $_SESSION['id_chofer'] = $data["id_empleado"];
                   $_SESSION['legajo'] = $data["legajo"];
                   $_SESSION['passwd'] = $_POST['txtPassword'];
                   $_SESSION['super'] = $data["super"];
                   $_SESSION['str'] = $data["estructura"];                   
                   $_SESSION['fleteros'] = 1;//$data["vistafletero"];
                   $_SESSION['admin'] = 0;
                   $query = mysqli_query($conn, "UPDATE passwords set ultimoacceso = NOW() WHERE id_chofer = $_SESSION[id_chofer]");
                   mysqli_free_result($query);
                   mysqli_close($conn);
                   header ("Location: ./ppal.php?pv=s");
  }
  else
  {

    }
}else{
	echo "<script>window.location='./index.php?e=0'</script>"; //El chofer esta dado de baja o es inexistente
}
if(isset($conexion)){
	mysql_close($conexion) or die(alerta(mysql_error()));
}

?>
<?php

?>
