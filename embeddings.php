<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include $_SERVER["DOCUMENT_ROOT"].'/modelo/utils/api_utils.php';

print '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">';
$ar = "<style>
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
			WHERE em.activo
			group by em.id_empleado
			order by apellido";

$result = mysqli_query($conn, $emples);
print "<div>
			<form method='POST' id='form-action'>
					<div class='row mt-4 ml-2'>
						<div class='col-4 ml-2'>
					<select name='emple' class='form-control ml-2'>";
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
</div><div class='col'>
		<input type='submit' name='Cargar' id='btnLoad' value='Ver embeddings' class='btn btn-primary'/>
		<input class='btn btn-danger' type='submit' name='delete-all' id='btn-delete-all' onclick=\"deleteAll(event);\" value='Eliminar todos los embeddings'/>
		<input class='btn btn-warning' type='submit' name='Last' value='Ultimos Accesos'/>
		<input class='btn btn-success' type='submit' name='config' value='Configuracion'/>
		<input class='btn btn-secondary' type='submit' name='ultimosEnvios' value='Ultimos Envios'/>
		<a class='btn btn-secondary' href='./registros.php'>Registro Paradas</a>
		<input type='hidden' id='action' name='action'/>
		</div>
		</div>
		</form>
		<script>
			function deleteAll(evt)
			{
				evt.preventDefault();
				if (confirm('Seguro eliminar todos los embeddings?'))
				{						
						document.getElementById('action').value='delete-all';
						var element = document.getElementById('form-action');
						element.submit();
				}
			}
		</script>";

function formConfiguration()
{
	$inf = $ms = "";
	$ds = $scale = false;
	$fila = 1;
	if (($gestor = fopen($_SERVER['DOCUMENT_ROOT'].'/config_file_puesh.csv', "r")) !== FALSE) 
	{
	    if (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) 
	    {
	        $inf = $datos[0];
	        $ms = $datos[1];
	        $ds = $datos[2];
	        $scale = $datos[3];
	    }
	    fclose($gestor);
	}

	$last = "";

	if (($gestor = fopen($_SERVER['DOCUMENT_ROOT'].'/las_configuration.csv', "r")) !== FALSE) 
	{
	    if (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) 
	    {
	        $last = $datos[0];
	    }
	    fclose($gestor);
	}

	/*$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/config_file_puesh.csv', 'w');

	$content = array(0.8, 91, 0,1);
	fputcsv($fp, $content);*/

	print '<br>
				 <hr>

				 <form class="needs-validation container" novalidate method="POST">
				 	<span class="ml-2 text-danger">Ultima configuracion: '.$last.'</span>
				 	<hr>
				  <div class="row ml-4">
				    <div class="col-lg-2 ml-4">
				    	<label for="inferencia">Inferencia</label>
				      <input required name="inferencia" id="inferencia" type="text" class="form-control ml-5" placeholder="inference" value="'.$inf.'">
				    </div>
				    <div class="col-lg-2">
				    	<label for="minsize">Min. Size</label>
				      <input type="text" id="minzsize" name="minsize" class="form-control" placeholder="minsize" value="'.$ms.'">
				    </div>				    
						  <div class="form-check col-lg-2 mt-4 ml-2">
							  <input class="form-check-input ml-4" type="checkbox" name="doublestep" id="defaultCheck1" '.($ds?"checked":"").'>
							  <label class="form-check-label" for="defaultCheck1">
							    Double Step
							  </label>
							</div>
						  <div class="form-check col-lg-2 mt-4">
							  <input class="form-check-input" type="checkbox" name="escala" id="scale" '.($scale?"checked":"").'>
							  <label class="form-check-label" for="scale">
							    Escala
							  </label>
							</div>
						</div>
						<div class="row mt-2">
								<div class="col">
									<input class="btn btn-secondary" type="submit" value="Enviar" name="pushconfig"/>
								</div>
						</div>
				</form>';
}


if (isset($_POST['pushconfig']))
{
		if ((!isset($_POST['inferencia'])) || (!isset($_POST['minsize'])))
		{
				print "<span class='text-danger'>Debe completar todos los campos!!</span>";
				formConfiguration();
				exit();
		}

		if ((!is_numeric($_POST['inferencia'])) || (!is_numeric($_POST['minsize'])))
		{
				print "<span class='text-danger'>El campo Inferencia y Min. Size deben ser numericos!!</span>";
				formConfiguration();
				exit();
		}

		$ds = isset($_POST['doublestep']);
		$scale = isset($_POST['escala']);

		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/config_file_puesh.csv', 'w');

		$content = array($_POST['inferencia'], $_POST['minsize'], $ds, $scale);
		fputcsv($fp, $content);


		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/las_configuration.csv', 'w');

		$content = array(date('d/m/Y - H:i:s'));
		fputcsv($fp, $content);

		print "<span class='text-success'>Configuracion almacenada exitosamente!!</span>";


/////////////////////////////////

	$curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://iotdevices.masterbus.net/api/login",
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS =>array(
                                          'user' => 'Leo',
                                          'pass' => 'leoMB'
                                      ),
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_HTTPHEADER => array(
                                'content-type' => 'application/json'
                                ),
  ));
  $response = curl_exec($curl);    
  curl_close($curl);
  $body = json_decode($response, true);


  $url = "http://iotdevices.masterbus.net/api/fcm/push";
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $headers = array(
     "access-token: $body[token]",
     "Content-Type: application/json",
  );
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
 
	$inf = 0.80; $ms = 91;
	$ds = false; $scale = true;
	$fila = 1;
	if (($gestor = fopen($_SERVER['DOCUMENT_ROOT'].'/config_file_puesh.csv', "r")) !== FALSE) 
	{
	    if (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) 
	    {
	        $inf = $datos[0];
	        $ms = $datos[1];
	        $ds = ($datos[2]?'true':'false');
	        $scale = ($datos[3]?'true':'false');
	    }
	    fclose($gestor);
	}

  $data = '{
				    "msg":{
				        "data":{
				            "action":"config",
				            "config": {
				                "inference":'.$inf.',
				                "minsize":'.$ms.',
				                "doublestep":'.$ds.',
				                "scale":'.$scale.'
				                }
				        }
				    }
				}';

//die($data.$response);

  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  $resp = curl_exec($curl);
  curl_close($curl);

  $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('$resp', now())";
  mysqli_query($conn, $insert);
  $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('CONFIG DETAIL $data', now())";
  mysqli_query($conn, $insert);

////////////////////////////////////




		formConfiguration();

}
elseif (isset($_POST['config']))
{
	formConfiguration();
}
elseif (isset($_POST['embed']))
{
	$datetime = new DateTime();
	$stamp = $datetime->getTimestamp();

	$updateStamp = "UPDATE empleados SET changeStamp = $stamp WHERE id_empleado = (SELECT id_empleado FROM embeddings WHERE id = $_POST[embed])";
	mysqli_query($conn, $updateStamp);

	try
	{

		$sqlDelete = "DELETE FROM embeddings where id = $_POST[embed]";
	//	die($sqlDelete);
		mysqli_query($conn, $sqlDelete);
		mysqli_close($conn);

		comunicatePush('FROM DELETE ONE EMBEDDING');
	}
	catch(Exception $e){ print $e->getMessage(); }

	print "Embedding eliminado exitosamente";
	print "<script type='text/javascript'>
				let button = document.getElementById('btnLoad');
				button.click();
			</script>";
}
elseif (isset($_POST['action']) && ($_POST['action'] == 'delete-all'))
{

	$sqlDelete = "DELETE FROM embeddings where id_empleado = $_POST[emple]";
	mysqli_query($conn, $sqlDelete);

	$datetime = new DateTime();
	$stamp = $datetime->getTimestamp();

	$updateStamp = "UPDATE empleados SET changeStamp = $stamp WHERE id_empleado = $_POST[emple]";
	mysqli_query($conn, $updateStamp);

	mysqli_close($conn);
	comunicatePush('FROM DELETE ALL EMBEDDING');
	
	print "Embedding eliminado exitosamente";
}
elseif (isset($_POST['Cargar']))
{
	$sqlInternos = "SELECT e.id, version, modelo, imagen, apellido, nombre, FROM_UNIXTIME(stamp) estamp FROM embeddings e left join empleados em on em.id_empleado = e.id_empleado 
				    where em.id_empleado = $_POST[emple]";
	$imagenes = mysqli_query($conn, $sqlInternos);
	  print "<script type='text/javascript'>

	  			function eliminar(id)
	  			{
	  				if (confirm('Seguro eliminar el embedding con ID '+id+'?'))
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
	  	print " <td class='text-center'> 
	  			<div>
				  <p>Stamp: $row[estamp]<br>
				  	 Version: $row[version]<br>
				  	 Modelo: $row[modelo]<br>
				  	 ID: $row[id]
				  </p>
				  <img src='data:image/png;base64, $row[imagen]' alt='Red dot' />
				  <form id='form-".$row['id']."' name='formdelete' method='POST'>
				  		<input class='btn btn-sm btn-danger mt-2' type='button' name='delete' value='Eliminar' onclick=\"eliminar($row[id]);\"/>
				  		<input type='hidden' name='embed' value='$row[id]'/>
				  		<input type='hidden' name='emple' value='$_POST[emple]'/>
				  </form>
				  <hr>
				</div>
				</td>";
		$count++;
	  }

  }
  elseif (isset($_POST['Last']))
  {
	$sqlInternos = "SELECT legajo, nrodoc, upper(concat(apellido,', ',nombre)) as empleado, a.stamp, if (sentido, 'INGRESO', 'EGRESO') as sentido, imagen
									FROM accesosregistro a
									left join embeddings emb on emb.id = a.idEmbedding
									join empleados e on e.id_empleado = a.id_empleado
									order by stamp DESC";
	$accesos = mysqli_query($conn, $sqlInternos);
	print "<div class='p-4'>
				<table class='table table-bordered table-striped table-hover p-2'>
				<thead>
				<tr>	
					<th>Legajo</th>
					<th>Documento</th>
					<th>Apellido, Nombre</th>
					<th>Sentido</th>
					<th>Horario</th>
					<th>Imagen</th>
				</tr>
				</thead>
				<tbody>";
	while ($row = mysqli_fetch_array($accesos))
	{
		$fecha = new DateTime();
		$fecha->setTimestamp($row['stamp']/1000);
		print "<tr class='align-middle'>
					<td>$row[legajo]</td>
					<td>".str_replace('.','',$row['nrodoc'])."</td>
					<td>$row[empleado]</td>
					<td>$row[sentido]</td>
					<td>".$fecha->format('d/m/Y - H:i:s')."</td>
					<td>".($row['imagen']?"<img src='data:image/png;base64, $row[imagen]' alt='Red dot' />":"")."</td>
			</tr>";
	}
	print "</tbody></table></div>";
  }
  elseif (isset($_POST['ultimosEnvios']))
  {
				$sqlInternos = "select *
												from
												(
												SELECT *
												FROM embeddingsSent e
												order by id DESC limit 10
												) u
												order by id";
				$accesos = mysqli_query($conn, $sqlInternos);
				print "<div class='p-4'>
							<table class='table table-bordered table-striped table-hover p-2'>
							<thead>
							<tr>	
								<th>Id</th>
								<th>Fecha</th>
								<th>Respuesta</th>
							</tr>
							</thead>
							<tbody>";
				while ($row = mysqli_fetch_array($accesos))
				{
					print "<tr class='align-middle'>
										<td>$row[id]</td>
										<td>$row[stamp]</td>
										<td>$row[response]</td>
								</tr>";
				}
				print "</tbody></table></div>";
  }