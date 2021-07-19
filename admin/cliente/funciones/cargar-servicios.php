<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');


if (!$ClienteSesion->getSesionBd()){
	header("Location: $redirCliLogin");
}

require_once ('../ws/ws-cliente.php'); // Libreria SOAP


// Parametros
$dia = 0;
$mes = 0;
$anio = 0; 
	
$listaServicios = null;
$msjPost = "";

if (isset($_POST) && isset($_POST['postServicio'])){
	$postServicio = $_POST['postServicio'];
	$jsonServicio = json_decode($_POST['postServicio']);
	$fecha = date_parse_from_format('Y-m-d', $jsonServicio->fecha);	 
	$dia = $fecha['day'];
	$mes = $fecha['month'];
	$anio = $fecha['year']; 
	
	// Listado de Servicios
	$wsServicios = wsClienteGetServicios($dia, $mes, $anio);
	$listaServicios = $wsServicios["ws_result"];
	if (empty($listaServicios)){
		$msjPost = "No se encuentran servicios disponibles para la fecha solicitada.";
	}
}else{
	$msjPost = "Debe ingresar una fecha válida para realizar la búsqueda.";
}
 
function colorFilaPorAsientos($cant){
	$color = "";
	 
	switch($cant){
		case (0):
			$color = "danger";
			break;
		case ($cant < 5):
			$color = "warning";
			break; 
		default:
			$color = "";
			break;
			
	}
	return $color;
}

if (empty($listaServicios)){
?>

<div class="alert alert-warning" role="alert">
	<p><?php echo $msjPost;?></p>
</div>

<?php }else { ?>
<p class="text-muted">Seleccione un servicio de la lista y presione el bot&oacute;n <b>Reservar</b> para continuar.
							<div id="tServicios" class="table-responsive">
						 
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr> 
													<th>Hora</th>
													<th>Origen</th>
													<th>Destino</th>
													<th>Precio</th>      
													<th>Asientos</th>
													<th>Opciones</th>
												</tr>
											</thead>
											<tbody>
											
											<?php foreach($listaServicios as $itemServicio){ 
												$cantAsientos = (int)$itemServicio['cantAsientos'];
											?>
												<tr  class="<?php echo colorFilaPorAsientos($cantAsientos);?>"> 
													<td class="serv_hora">
														<p><?php echo $itemServicio['hora'];?></p>
													</td>
													<td class="serv_origen">
														<p><?php echo $itemServicio['origen'];?></p>
													</td>												 
													<td class="serv_destino">
														<p><?php echo $itemServicio['destino'];?></p>
													</td>
													<td class="text-right serv_precio" >
														<span  >$ </span><p style="display: inline-block;"><?php echo $itemServicio['precio'];?></p>
													</td>
													 <td class="text-right serv_asientos">
														<p><?php echo $itemServicio['cantAsientos'];?></p>
													</td>
													<td class="text-center">
														<div class="btn-group "  >			                                  
														  <a class="btn btn-default <?php echo ($cantAsientos <= 0) ? "disabled": "";?> btnServReservar " data-codserv="<?php echo $itemServicio['codigo_serv'];?>">Reservar</a>
														</div>
													</td>
												</tr>
											<?php } // fin foreach?>
											
											</tbody>
										</table>
									
									</div>
<?php } ?>


  <script type="text/javascript">
		   $('.btnServReservar').on('click',function(ev){
				 ev.preventDefault();
				 var cod_servicio = $(this).data('codserv');
				 var hora = $(this).parents('tr').find('.serv_hora p').text();
				 var origen = $(this).parents('tr').find('.serv_origen').text();
				 var destino = $(this).parents('tr').find('.serv_destino').text();
				 var precio = $(this).parents('tr').find('.serv_precio p').text();
				var asientos = $(this).parents('tr').find('.serv_asientos').text();
				asientos = parseInt(asientos);
		 
				 setServicioSelecc(cod_servicio, hora, origen, destino, precio, asientos);
		   });
		   
		   
		 </script>