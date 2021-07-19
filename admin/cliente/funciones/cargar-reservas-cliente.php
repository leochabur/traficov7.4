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
	
$clienteReservas = null;
$msjPost = "";

if (isset($_POST) && isset($_POST['postServicio'])){
	$postServicio = $_POST['postServicio'];
	$jsonServicio = json_decode($_POST['postServicio']);
	$fecha = date_parse_from_format('Y-m-d', $jsonServicio->fecha);	 
	$dia = $fecha['day'];
	$mes = $fecha['month'];
	$anio = $fecha['year']; 
	
	$idCliente = $Cliente->getClienteId();

	// Cliente - Reservas
	$wsClienteReservas = wsClienteGetReservas($idCliente, $dia, $mes, $anio);
	$clienteReservas = $wsClienteReservas["ws_result"];
	if (empty($clienteReservas)){
		$msjPost = "No tiene reservas realizadas para la fecha ingresada.";
	}
}else{
	$msjPost = "Debe ingresar una fecha válida para realizar la búsqueda.";
}
 

if (empty($clienteReservas)){
?>

<div class="alert alert-warning" role="alert">
	<p><?php echo $msjPost;?></p>
</div>

<?php }else { ?>

 <div id="msjReservaBaja" class="alert hide " role="alert">
											<p  class="text-muted "></p>
								</div>
							<div id="tReservasCliente" class="table-responsive">
						 
								<table class="table table-striped table-bordered table-hover">
								<thead>
								<tr> 
									<th>Hora</th>
									<th>Origen</th>
									<th>Destino</th>
									<th>Precio</th>     
									<th>Opciones</th>    	
								</tr>
								</thead>
								<tbody>
								
								<?php foreach($clienteReservas as $itemReserva){ ?>
				                	<tr>
				                	 
				                		<td>
				                			<p><?php echo $itemReserva['hora'];?></p>
				                		</td>
				                		<td>
				                			<p><?php echo $itemReserva['origen'];?></p>
				                		</td> 
				                		<td>
				                			<p ><?php echo $itemReserva['destino'];?></p>
				                		</td>
				                		 <td>
				                			<p ><?php echo $itemReserva['precio'];?></p>
				                		</td> 
				                		<td>
				                			<div class="btn-group"  >		
												<?php if (isset($itemReserva["estado"] )){ ?>
												<p class="label">Estado</p>
												<?php }else{ ?>
												<a class="btn btn-danger btnReservaBaja" data-codreserva="<?php echo $itemReserva['codigoReserva']; ?>">Cancelar</a>
												<?php }?>
		                                    </div>
				                		</td>
				                	</tr>
			                	<?php } // fin foreach?>
			                	
								</tbody>
								</table>
							
							</div>
							<div class="total">
								<p>Total <?php echo count($clienteReservas);?></p>
							</div>
<?php } ?>

  <script type="text/javascript">
			
			$(document).on("click", "#tReservasCliente .btnReservaBaja", function(ev) {
		   
				 ev.preventDefault();
				 var codreserva = $(this).data('codreserva'); 
				 
				var $msjReservaBaja = $("#msjReservaBaja");
				$msjReservaBaja.addClass("hide");
				$msjReservaBaja.find('p').html("");			
				
				 reservaCancelar({postCodReserva: codreserva}, function(r){
					 $msjReservaBaja.fadeIn(500);
					 if (r.ok){
						 $(this).parents("tr").remove();
						$msjReservaBaja.removeClass("hide").addClass("alert-success");		
						
					}else{
						$msjReservaBaja.removeClass("hide").addClass("alert-danger");
					}
					
					$msjReservaBaja.find('p').html(r.msj);	
					$msjReservaBaja.fadeOut(2000);
				 });
		   });
		   
		   
		   function reservaCancelar(codreserva, callBack){
			   if (codreserva == ""){
				   return false;
			   }
			   $.ajax({
					type: "POST",
					cache: false,
					data: codreserva,
					url: "cliente/funciones/cli-post-reserva-baja.php",
					dataType: "json",
					success: function(r) 
					{          
						callBack(r);
					},
					error: function(xhr, ajaxOptions, thrownError)
					{                 
					   callBack(r);           
					} 
				});
		   }
		   
		 </script>