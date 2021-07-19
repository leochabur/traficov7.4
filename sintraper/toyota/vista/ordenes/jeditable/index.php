<?php 
include("conexion.php");

//Seleccionamos los registros de la tabla
$query = mysql_query("SELECT * FROM usuarios", $cn);

$paises = array(
			"1"=>"Argentina",
			"2"=>"Bolivia",
			"3"=>"Peru",
			"4"=>"Chile"
		 );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Editando registros de una tabla con Jeditable plugin jQuery.</title>
<link rel="stylesheet" type="text/css" href="style.css"/>

<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery.jeditable.js"></script>
<script type="text/javascript" src="js.js"></script>

</head>
<body>
	<fieldset id="content">
    	<legend>Editando registros de una tabla con Jeditable plugin jQuery.</legend>
    	<table id="table">
            <thead>
            	<tr class="head">
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Edad</th>
                    <th>Email</th>
                    <th>Pais</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
            	<?php
				while($row = mysql_fetch_array($query))
				{
					$id = $row['id_usuario'];
					?>
					<tr>
                        <td><div class="text" id="nombre_usuario-<?php echo $id ?>"><?php echo $row['nombre_usuario']?></div></td>
                        <td><div class="text" id="apellido_usuario-<?php echo $id ?>"><?php echo $row['apellido_usuario']?></div></td>
                        <td><div class="text" id="edad_usuario-<?php echo $id ?>"><?php echo $row['edad_usuario']?></div></td>
                        <td><div class="text" id="email_usuario-<?php echo $id ?>"><?php echo $row['email_usuario']?></div></td>
                        <td><div class="select" id="id_pais-<?php echo $id ?>"><?php echo $paises[$row['id_pais']]?></div></td>
                        <td><div class="textarea" id="observacion_usuario-<?php echo $id ?>"><?php echo $row['observacion_usuario']?></div></td>
                    </tr>
					<?php
				}
				 ?>
            </tbody>
        </table>
    </fieldset>
	
</body>
</html>