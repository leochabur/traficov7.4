<?php


include $_SERVER["DOCUMENT_ROOT"].'/modelo/utils/api_utils.php';

?>
<!--  jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>



<!-- Bootstrap Date-Picker Plugin -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
<?php

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

$emples = "SELECT  upper(interno) as interno
						FROM ap_registro_paradas a
						group by interno
						order by interno";

$result = mysqli_query($conn, $emples);
?>
<div class='container'>
			<form method='POST' id='form-action'>
					<div class='row mt-4 ml-2'>
						<div class='col-2 ml-2'>
							<select name='interno' class='form-control ml-2'>
										<option value="0">Todos</option>
								<?php
										while ($row = mysqli_fetch_array($result))
										{
												print "<option value='$row[interno]'>$row[interno]</option>";									
										}			
									?>
							</select>				
						</div>
						<div class='col-lg-2'>
								<input class="form-control" value="<?php echo $_POST['desde']; ?>" id="desde" name="desde" placeholder="MM/DD/YYYY" type="text" autocomplete="off"/>
						</div>
						<div class='col-lg-2'>
								<input class="form-control" value="<?php echo $_POST['hasta']; ?>" id="hasta" name="hasta" placeholder="MM/DD/YYYY" type="text" autocomplete="off"/>
						</div>
						<div class='col-lg-2'>
								<input type='submit' name='cargar' id='btnLoad' value='Ver Registros' class='btn btn-primary'/>
						</div>
						<div class='col-lg-2'>
								<a href="./embeddings.php" class='btn btn-success'><< Volver </a>
						</div>
					</div>
		</form>

<?php

	if (isset($_POST['cargar']))
	{

			if ($_POST['desde'] && $_POST['hasta'])
			{
					$hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
					$desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);

					$interno = "";
					if ($_POST['interno'])
					{
						$interno = " AND interno = '$_POST[interno]'";
					}

					$sql = "SELECT interno, servicio, parada, date_format(FROM_UNIXTIME(arrival), '%d/%m/%Y - %H:%i:%s') as horario,
									     if (duration = 0, 'SALTEO LA PARADA', if (duration = -1, 'NO REALIZO DETENCION', SEC_TO_TIME(duration))) as duracion,
									     latitud, longitud
									FROM ap_registro_paradas a
									where FROM_UNIXTIME(arrival) BETWEEN '".$desde->format('Y-m-d')." 00:00:00' AND '".$hasta->format('Y-m-d')." 23:59:59' $interno
									order by interno, servicio, horario";

					$result = mysqli_query($conn, $sql);

					print "<table class='table table-bordered table-striped table-hover p-2' style='font-size: small'>
								<thead>
								<tr>	
									<th>Interno</th>
									<th>Servicio</th>
									<th>Parada</th>
									<th>Horario</th>
									<th>Duracion</th>
									<th>Lat.</th>
									<th>Long.</th>
								</tr>
								</thead>
								<tbody>";
					while ($row = mysqli_fetch_array($result))
					{
						print "<tr>
									<td>$row[interno]</td>
									<td>$row[servicio]</td>
									<td>$row[parada]</td>
									<td>$row[horario]</td>
									<td>$row[duracion]</td>
									<td>".(float)$row[latitud]."</td>
									<td>".(float)$row[longitud]."</td>
							</tr>";
					}
					print "</tbody></table></div>";
				  

			}
	}
	mysqli_close($conn)

?>


</div>
		<script>

    $(document).ready(function(){

      var options={
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        autoclose: true,
      };
      $('#desde, #hasta').datepicker(options);
    })
		</script>;