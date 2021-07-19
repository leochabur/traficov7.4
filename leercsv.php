<?php
  set_time_limit(0);

  include ('./controlador/bdadmin.php');
  include ('./controlador/ejecutar_sql.php');

  $conn = conexcion(true);

$archivo = fopen("correos.csv", "r");
//Lo recorremos
while (($datos = fgetcsv($archivo, 1000, ";")) == true) 
{
	$sql = "UPDATE empleados SET email = '$datos[0]' WHERE legajo = $datos[1] AND id_empleador = 1 AND activo";
	mysqli_query($conn, $sql);

}
//Cerramos el archivo
fclose($archivo);
?>