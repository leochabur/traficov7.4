<?php

print "<style>
tr:nth-child(even) {
  background-color: #f2f2f2;
}
td {
    padding: 10px;
}
</style>";

$conn = new mysqli('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');

$emples = "SELECT concat(apellido,', ', nombre) as nombre, e.id_empleado
			FROM embeddings e
			inner join empleados em on em.id_empleado = e.id_empleado
			group by em.id_empleado
			order by apellido";

$result = mysqli_query($conn, $emples);
print "<div>
			<form method='POST'>
			<select name='emple'>";
while ($row = mysqli_fetch_array($result))
{
	if ((isset($_POST['emple'])) && ($_POST['emple'] == $row['id_empleado']))
	{
		
		print "<option value='$row[id_empleado]' selected='selected'>$row[nombre]</option>";
	}
	else
	{
		print "<option value='$row[id_empleado]'>$row[nombre]</option>";
	}
}			
print "</select>
		<input type='submit' name='Cargar' id='btnLoad' value='Ver embeddings'/>
		<input type='submit' name='Last' value='Ultimos Accesos'/>
		</form>";


if (isset($_POST['embed']))
{
	
	$sqlDelete = "DELETE FROM embeddings where id = $_POST[embed]";
	mysqli_query($conn, $sqlDelete);
	mysqli_close($conn);
	print "Embedding eliminado exitosamente";
	print "<script type='text/javascript'>
				let button = document.getElementById('btnLoad');
				button.click();
			</script>";
}
elseif (isset($_POST['Cargar']))
{
	$sqlInternos = "SELECT e.id, version, modelo, imagen, apellido, nombre, FROM_UNIXTIME(stamp) estamp FROM embeddings e left join empleados em on em.id_empleado = e.id_empleado 
				    where em.id_empleado = $_POST[emple]";
	$imagenes = mysqli_query($conn, $sqlInternos);
	  print "<script type='text/javascript'>

	  			function eliminar(id)
	  			{
	  				if (confirm('Seguro eliminar el embedding?'))
	  				{
						let element = document.getElementById('form-'+id);
						console.log(element);
						element.submit();
	  				}
	  			}

	  		</script>";
	  
	  $print = false;
	  $count = 0;
	  while ($row = mysqli_fetch_array($imagenes))
	  {
	  	if (!$print)
	  	{
	  		print "<br><div>Apellido: $row[apellido]</div>
	  			   <br><div>Nombre: $row[nombre]</div>
	  			   <hr>";
	  	    $print = true;
	  	    print "<table width='100%'>
	  	    		<tr>";
	  	}
	  	if ($count == 5)
	  	{
	  		print "</tr><tr>";
	  		$count = 0;
	  	}
	  	print " <td> 
	  			<div>
				  <p>Stamp: $row[estamp]<br>
				  	 Version: $row[version]
				  	 Modelo: $row[modelo]
				  </p>
				  <img src='data:image/png;base64, $row[imagen]' alt='Red dot' />
				  <form id='form-".$row['id']."' name='formdelete' method='POST'>
				  		<input type='button' name='delete' value='Eliminar' onclick=\"eliminar($row[id]);\"/>
				  		<input type='hidden' name='embed' value='$row[id]'/>
				  		<input type='hidden' name='emple' value='$_POST[emple]'/>
				  </form>
				</div>
				</td>";
		$count++;
	  }

  }
  elseif (isset($_POST['Last']))
  {
	$sqlInternos = "SELECT legajo, nrodoc, upper(concat(apellido,', ',nombre)) as empleado, stamp, if (sentido, 'INGRESO', 'EGRESO') as sentido
					FROM accesosregistro a
					join empleados e on e.id_empleado = a.id_empleado
					order by stamp DESC";
	$accesos = mysqli_query($conn, $sqlInternos);
	print "<table border='1' width='100%'>
				<tr>	
					<td>Legajo</td>
					<td>Documento</td>
					<td>Apellido, Nombre</td>
					<td>Sentido</td>
					<td>Horario</td>
				</tr>";
	while ($row = mysqli_fetch_array($accesos))
	{
		$fecha = new DateTime();
		$fecha->setTimestamp($row['stamp']/1000);
		print "<tr>
					<td>$row[legajo]</td>
					<td>".str_replace('.','',$row['nrodoc'])."</td>
					<td>$row[empleado]</td>
					<td>$row[sentido]</td>
					<td>".$fecha->format('d/m/Y - H:i:s')."</td>
			</tr>";
	}
	print "</table>";
  }
