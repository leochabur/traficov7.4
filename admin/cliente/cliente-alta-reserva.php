<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');


if (!$ClienteSesion->getSesionBd()){	
	header("Location: $redirCliLogin");
}

require_once ('ws/ws-cliente.php'); // Libreria SOAP
  
$webTitulo = WEB_TITULO . " - Clientes - Alta Reserva.";

?>
<!DOCTYPE html>
<html>
<head>

    <?php require_once ($raiz.'incs/inc-meta.php');?>
    
    <title><?php echo $webTitulo;?></title>
	<base href="<?php echo RUTA_BASE;?>" target="_top" />
    
    <?php require_once ($raiz.'incs/inc-css.php');?> 
    <link rel="stylesheet" href="<?php echo URL_WEB."css/css-cliente.css";?>">
     
      <?php require_once ($raiz.'incs/inc-js.php');?> 
</head>

<body>
	<?php require_once ($raiz.'incs/inc-pre-nav.php');?>
    <?php require_once ($raiz.'incs/inc-nav2.php');?>
    
     <!-- #portada Cliente-->
	<div id="portada-cliente-consulta" >
		<div class="container">
			<div id="servicios">
					<div class="row">
						<div class="col-md-12 col-sm-12 titulo-encabezado text-center">
				            <h2>Nueva <span>Reserva</span></h2>
				            <p>Complet&aacute; los datos solicitados para dar de alta una reserva.</p>	
				      	</div>
				      	<div><span class="divisor100"></span>	</div>
			      	</div>
		      	</div>
		</div>
	</div>
	<!-- /#portada Cliente-->
	
	<!-- Consulta Cliente -->
	<div id="clienteconsulta">
		<div class="container ">
			   
			  <div class="row">
			  	<?php require_once("incs/inc-cliente-sidebar.php");?>
			  			  
			 	<div class="col-xs-12 col-sm-9 col-md-9 ">
			  		<!-- FILTROS -->
					  		<div class="portlet box red ">
								<div class="portlet-title">
									<div class="caption">
										<i class="fa fa-search fa-fw"></i>Servicios - Listado de servicios disponibles.
									</div> 
									<div class="tools">
											<a href="javascript:;" class="collapse">								 
											</a>
											
											<a href="javascript:;" class="reload">								  
											</a> 
										 
										</div>
								</div>
								<div id="boxServicios" class="portlet-body ">
									<p class="text-muted">Ingrese una fecha v&aacute;lida y presione el bot&oacute;n <b>Buscar</b>  para ver los servicios disponibles.
									<div id="msjFiltroServicio" class="alert alert-danger hide " role="alert">
										<p  class="text-muted "></p>
									</div>
									
									<div class="col-sm-12" style="padding-top: 20px; margin-bottom: 30px;">
										<form id="frmServicios" class="form-inline" role="form" method="POST">
											 
												<div class="form-group">        
													<label for="txtservicioFecha" class="control-label "  >Fecha:</label>  
													<div class="input-group " >
													  <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
													  <input type="date" class="form-control " id="txtservicioFecha" value="<?php echo date('Y-m-d');?>">											      
													</div>                       
																									 
												</div>			
												   
												<button id="btnbuscar" type="submit" class="btn btn-default btn-lg" data-loading-text="Buscando...">Buscar</button> 
											  
										</form>
									</div>
									 <div class="clearfix"></div>
									 
									<div class="listaServiciosLoading hide text-center" data-toggle="tooltip" data-placement="bottom" title="Cargando servicios..." >
										<i class="fa fa-spinner fa-spin fa-3x"></i>
									</div>
									<div id="listaServicios"></div>
									
									 
								</div>
							</div>
					  		<!-- FIN FILTROS -->
			  				
			  				<div id="reservas">
			  					<div class="portlet box red">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-calendar fa-fw"></i>Alta de Reserva - Complete los datos a continuaci&oacute;n
										</div>
										
									</div>
									<div class="portlet-body altaReservas">
										<p class="text-muted">Seleccione un servicio de la lista de <b>Servicios</b> y complete los datos. Presione el bot&oacute;n <b>Confirmar</b> para realizar la reserva.
										<div id="msjReserva" class="alert hide " role="alert">
											<p  class="text-muted "></p>
										</div>
										<div id="reservaServicioSelecc" class="alert alert-dismissible hide" style="background-color: #f7f7f9;" role="alert"> 
											<button id="btnCancelarServicio" type="button" class="close" aria-label="Cancelar"><span aria-hidden="true">&times;</span></button>
											<p>Servicio seleccionado: </p><span id="datosServicio"></span>
										</div>
										
										<form id="frmReserva" class="form-horizontal" role="form" method="POST"> 
											 
											<input class="form-control hidden" type="text" id="txtreservaCodServ" readonly >
											 
											<div class="form-group">        
				                            	<label for="txtreservaLSubida" class="col-sm-2 control-label"  >Lugar subida:</label>  
				                            	<div class="col-sm-8 input-group " >
											      <div class="input-group-addon"><i class="fa fa-arrow-right"></i></div>
											      <input type="text" class="form-control " id="txtreservaLSubida" >											      
											    </div>                       
				                                			                                	 
											</div>			
											<div class="form-group">           
			                                    <label for="txtreservaLBajada" class="col-sm-2 control-label ">Lugar bajada:</label>
				                                <div class="col-sm-8 input-group">
											      <div class="input-group-addon"><i class="fa fa-arrow-left"></i></div>
											      <input type="text" class="form-control" id="txtreservaLBajada" > 
			                                	</div>
				                            </div>  	
											<div class="form-group">           
				                                    <label for="txtreservaAsientos" class="col-sm-2 control-label ">Asientos:</label>
					                               <div class="col-sm-2 input-group">
													  <div class="input-group-addon"><i class="fa fa-users"></i></div>
													  <input type="number" class="form-control" id="txtreservaAsientos" min="0" max="0">  
													</div>
													
											</div> 											
											<div class="form-group">           
				                                    <label for="txtreservaPrecio" class="col-sm-2 control-label ">Precio:</label>	 
													<div class="col-sm-2 input-group">
													  <div class="input-group-addon"><i class="fa fa-dollar"></i></div>
													  <input id="txtreservaPrecio" class="form-control" type="text" readonly value="0">
													</div>
													

													
											</div> 
											 <div class="form-group">
												<div class="col-sm-offset-2  ">
													<button id="btnReserva" type="submit" class="btn  btn-default btn-lg pull-left" data-loading-text="Procesando...">Confirmar</button> 
												</div>
											</div>
											
											<div class="clearfix"></div>
											
										</form>
									</div>
								</div>
			  				</div>
					
				</div>
			</div>
		</div>
	</div>
	<!-- /#Consulta Cliente -->
	 
 
    <?php require_once ($raiz.'incs/footer4.php');?> 
        
    <script src="<?php echo (URL_WEB.'js/headroom.min.js');?>"></script>
	<script src="<?php echo (URL_WEB.'js/jQuery.headroom.min.js');?>"></script>
	<script src="<?php echo (URL_WEB.'js/principal.js');?>"></script>
	<script src="<?php echo (URL_WEB.'js/jquery.blockUI.js');?>"></script>  
    <script type="text/javascript">
       
	
    $(document).ready(function() {

    	$('[data-toggle="tooltip"]').tooltip();
    	 
		 
		$('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function (e) {
			e.preventDefault();
			var el = $(this).closest(".portlet").children(".portlet-body");
			var url = $(this).attr("data-url");
			var error = $(this).attr("data-error-display");
			if (url) {
				
				$.blockUI({ target: el, iconOnly:true  });  
				reloadServicios();
				
			} else {
				// for demo purpose
				$.blockUI({target: el, iconOnly: true});
				window.setTimeout(function () {
					$.unblockUI(el);
				}, 1000);
			}            
		});

		// load ajax data on page init
		$('.portlet .portlet-title a.reload[data-load="true"]').click();

		$('body').on('click', '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand', function (e) {
			e.preventDefault();
			var el = $(this).closest(".portlet").children(".portlet-body");
			if ($(this).hasClass("collapse")) {
				$(this).removeClass("collapse").addClass("expand");
				el.slideUp(200);
			} else {
				$(this).removeClass("expand").addClass("collapse");
				el.slideDown(200);
			}
		});

		$("#frmServicios").submit(function(ev){
			ev.preventDefault();

			var $btnsubmit = $("#btnbuscar");
						
			var $msjFiltro = $("#msjFiltroServicio");
			$msjFiltro.addClass("hide");
			$msjFiltro.find('p').html("");
			var fServicio = {
						'fecha' : $("#txtservicioFecha").val()
					};

			if (fServicio.fecha == ""  )
			{
				$msjFiltro.removeClass("hide");
				$msjFiltro.find('p').html("Debe ingresar al menos un parámetro de búsqueda.");
				$btnsubmit.button('reset'); 
				return false;
			}
				$btnsubmit.button('loading'); 
			 
			var fServicioJson = JSON.stringify(fServicio);
			
			$("#listaServicios").html("");  
					
			clearServicioSelecc();
			
			reloadServicios({postServicio: fServicioJson}, function(result){

				$btnsubmit.button('reset'); 	
				$.unblockUI();				                           
				$("#listaServicios").html(result);          			
				  
			});
			
			
			return;

		});
        
		$("#frmReserva").submit(function(ev){
			ev.preventDefault();

			var $btnsubmit = $("#btnReserva");			
			
			var $msjFiltro = $("#msjReserva");
			$msjFiltro.addClass("hide");
			$msjFiltro.find('p').html("");			
			
			var pReserva = {
						'cod_serv' 	: $("#txtreservaCodServ").val(),
						'subida'	: $("#txtreservaLSubida").val(),
						'bajada'	: $("#txtreservaLBajada").val(),
						'precio'	: servicio_selec_precio,
						'asientos'	: $("#txtreservaAsientos").val()
					};
			
			
			if (pReserva.codServ == "" || 
				pReserva.subida == "" || 
				pReserva.bajada == "" || 
				pReserva.asientos == "" || pReserva.asientos == 0 )
			{
				$msjFiltro.removeClass("hide").addClass("alert-danger");
				$msjFiltro.find('p').html("Debe completar los datos solicitados.");
				$btnsubmit.button('reset'); 
				return false;
			}
				 
			if (pReserva.precio <= 0){
				var total = servicio_selec_precio * pReserva.precio;
				updatePrecioReserva(total);
			}
			
			$btnsubmit.button('loading'); 
			
			var pReservaJson = JSON.stringify(pReserva);
 
			enviarReserva({postReserva: pReservaJson}, function(result){

				$btnsubmit.button('reset'); 	
				if(result.ok){
					$msjFiltro.removeClass("hide").addClass("alert-success");					
				}else{
					$msjFiltro.removeClass("hide").addClass("alert-danger");
				}
				
				$msjFiltro.find('p').html(result.msj);	
				$msjFiltro.fadeOut(2000);
				clearServicioSelecc();
			});
			
			return;

		});
		
		$("#btnCancelarServicio").click(function(ev){
			ev.preventDefault();
			clearServicioSelecc();		
		});
		
		$("#txtreservaAsientos").change(function(ev){
			var total = servicio_selec_precio * $(this).val();
			updatePrecioReserva(total);
		});
    });
	 
	 var servicio_selec_precio = 0;
	 function setServicioSelecc(cod, hora, origen, destino, precio, asientos){		 
		$("#reservaServicioSelecc").removeClass("hide");
		 $("#txtreservaCodServ").val(cod);		
		 $("#reservaServicioSelecc #datosServicio").html("<b>Hora</b>: " +hora + " <b>Origen</b>: " + origen + " <b>Destino</b>: "+ destino);
		 $("#txtreservaAsientos").val(0);		
		 $("#txtreservaAsientos").attr('max', asientos);		
		 $("#txtreservaAsientos").attr('min', 0);	
		 servicio_selec_precio = precio;
	 }
	 
	 function updatePrecioReserva(total){
		 
			$("#txtreservaPrecio").val(total);		
	 }
	 
	 function clearServicioSelecc(){ 
		$("#reservaServicioSelecc").addClass("hide");
		$("#txtreservaCodServ").val("");	
		$("#txtreservaLBajada").val("");
		$("#txtreservaLSubida").val("");
		$("#txtreservaAsientos").val(0);	
		$("#txtreservaAsientos").attr('max', 0);
		updatePrecioReserva(0);
	 }
	 
    function reloadServicios(pData, callBack){
        $(".listaServiciosLoading").removeClass("hide");
    	$.ajax({
            type: "POST",
            cache: false,
            data: pData,
            url: "cliente/funciones/cargar-servicios.php",
            dataType: "html",
            success: function(r) 
            {          
            	callBack(r);
            },
            error: function(xhr, ajaxOptions, thrownError)
            {                 
               alert("Error");                
            }, 
            complete: function(){
            	$(".listaServiciosLoading").addClass("hide");
            }
        });
    }
	
	function enviarReserva(pData, callBack){
		 
    	$.ajax({
            type: "POST",
            cache: false,
            data: pData,
            url: "cliente/funciones/cli-post-reserva.php",
            dataType: "json",
            success: function(r) 
            {          
            	callBack(r);
            },
            error: function(xhr, ajaxOptions, thrownError)
            {                 
				callBack(r);  
            }, 
            complete: function(){
            	 
            }
        });
	}
	
    </script>

</body>

</html>
