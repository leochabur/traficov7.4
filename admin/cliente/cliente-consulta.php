<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');


if (!$ClienteSesion->getSesionBd()){	
	header("Location: $redirCliLogin");
}

require_once ('ws/ws-cliente.php'); // Libreria SOAP
 
$webTitulo = WEB_TITULO . " - Clientes - Consultas";

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
				            <h2>Tus <span>Reservas</span></h2>
				            <p>Consult&aacute; tus reservas realizadas.</p>	
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
			  		  
						<div id="boxReservas" class="portlet box red">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-calendar fa-fw"></i>Mis Reservas
								</div>
								<div class="tools">
									<a href="javascript:;" class="collapse">								 
									</a>
									
									<a href="javascript:;" class="reload">								  
									</a> 
								 
								</div>
							</div> 
							<div class="portlet-body">
								<p class="text-muted">Ingrese una fecha v&aacute;lida y presione el bot&oacute;n <b>Buscar</b>  para ver sus reservas.
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
								
								 <div class="listaReservasLoading hide text-center" data-toggle="tooltip" data-placement="right" title="Cargando reservas..." >
									<i class="fa fa-spinner fa-spin fa-3x"></i>
								</div>
								<div id="listaReservas"></div>
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
    	
		var $listaReservas = $("#listaReservas");
		 
    		
            $('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function (e) {
                e.preventDefault();
                var el = $(this).closest(".portlet").children(".portlet-body");
                var url = $(this).attr("data-url");
                var error = $(this).attr("data-error-display");
                if (url) {
                    
		            $.blockUI({ target: el, iconOnly:true  });  
                    reloadReservasCliente();
                    
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
			 
				var fServicioJson = getFechaServicio();
				if (!fServicioJson  )
				{
					$msjFiltro.removeClass("hide");
					$msjFiltro.find('p').html("Debe ingresar una fecha válida.");
					$btnsubmit.button('reset'); 
					return false;
				}
				
				$btnsubmit.button('loading'); 
				
				$listaReservas.html("");  
				  
	            $.blockUI({ message:  '<h1><i class="fa fa-spinner fa-pulse"></i> Buscando reservas... Por favor aguarde.</h1>'  }); 
	            
	            reloadReservasCliente({postServicio: fServicioJson}, function(result){

	           		$.unblockUI();				   
	           		$btnsubmit.button('reset'); 	                        
           		 	$listaReservas.html(result);          			
		              
	           	});

	            return;

         });
        
    });

	function getFechaServicio(){
				 var fServicio = {
							'fecha' : $("#txtservicioFecha").val()
						};

				if (fServicio.fecha == ""  )
				{
					return null;
				}
				
				return JSON.stringify(fServicio);
	}
	
    function reloadReservasCliente(pData, callBack){
        $(".listaReservasLoading").removeClass("hide");
    	$.ajax({
            type: "POST",
            cache: false,
            data: pData,
            url: "cliente/funciones/cargar-reservas-cliente.php",
            dataType: "html",
            success: function(r) 
            {          
            	callBack(r);
            },
            error: function(xhr, ajaxOptions, thrownError)
            {                 
               callBack(r);           
            }, 
            complete: function(){
            	$(".listaReservasLoading").addClass("hide");
            }
        });
    }
    </script>

</body>

</html>
