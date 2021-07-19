<?php 

include('./controlador/bdadmin.php');

$conn = conexcion(true);

if (($gestor = fopen("telefonos.csv", "r")) !== FALSE) 
{
    while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
    	$insert = "INSERT INTO segVial_telefonos (numero, alias, usuario, ubicacion, tipo, servicio, ultimoModelo, imei)
    			VALUES($datos[1], '$datos[2]', '$datos[3]', '$datos[4]', '$datos[5]', '$datos[6]', '$datos[7]', '$datos[8]')";
    	mysqli_query($conn, $insert);
    }
    fclose($gestor);
}


?>
