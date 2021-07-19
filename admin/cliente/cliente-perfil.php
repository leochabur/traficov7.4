<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
  

if (!$ClienteSesion->getSesionBd()){	
	header("Location: $redirCliLogin");
}
 
$webTitulo = WEB_TITULO . " - Su cuenta";

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
    <script src="<?php echo (URL_WEB.'js/headroom.min.js');?>"></script>
	<script src="<?php echo (URL_WEB.'js/jQuery.headroom.min.js');?>"></script>
	<script src="<?php echo (URL_WEB.'js/principal.js');?>"></script> 
</head>

<body>
	<?php require_once ($raiz.'incs/inc-pre-nav.php');?>
    <?php require_once ($raiz.'incs/inc-nav2.php');?>
    
     <!-- #portada Cliente-->
	<div id="portada-cliente-perfil" >
		<div class="container">
			<div id="servicios">
					<div class="row">
						<div class="col-md-12 col-sm-12 titulo-encabezado text-center">
				            <h2>Tu <span>Cuenta</span></h2>
				            <p>Pod&eacute;s modificar los datos de tu cuenta si no son correctos.</p>	
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
		 
					<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-cog fa-fw"></i>Datos principales de tu Cuenta
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">								 
								</a>
								
								<a href="javascript:;" class="reload">								  
								</a> 
							 
							</div>
						</div>
						<div class="portlet-body">
							<div class="col-lg-12 alert alert-warning">     
			                 	<p>Complete los campos a continuaci&oacute;n y presione el bot&oacute;n <strong>Guardar</strong> al final del formulario.</p>
			            	</div>
							<form id="frmcliente" class="form-horizontal" role="form" method="POST"   >
	                              
								 <div class="col-md-12">   
											 	 
	                                     <div class="form-group">
	                                        <label for="txtNombre" class="col-md-2 control-label ">Nombre *</label>
	                                        <div class="col-md-8">
						                        <input type="text" class="form-control" id="txtNombre" name="txtNombre" placeholder="Ingrese su nombre..." value="<?php echo $Cliente->getNombre();?>">
						                    </div>		                                        
                                        	                                        	
	                                      </div>
	                                      <div class="form-group">
	                                        <label for="txtApellido" class="col-md-2 control-label " >Apellido *</label>
	                                        <div class="col-md-8">
						                        <input type="text" class="form-control" id="txtApellido" name="txtApellido" placeholder="Ingrese su apellido..." value="<?php echo $Cliente->getApellido();?>">
						                    </div>				                                          
                                         				                                          
	                                      </div>
	
	                                      <div class="form-group">
	                                          <label for="txtEmail" class="col-md-2 control-label " >Email *</label>	
	                                          <div class="col-md-8">
						                        <div class="input-group">                            
                                                <div class="input-group-addon"><i class="fa fa-at"></i></div>
                                                	<input id="txtEmail" name="txtEmail" class="form-control" type="email" placeholder="Ingrese una cuenta de correo v&aacute;lida..." value="<?php echo $Cliente->getEmail();?>">
                                              </div> 
						                    </div>			                                           
                                              
	                                      </div>                    
	                                        <div class="form-group">
	                                          <label for="txtTelefono" class="col-md-2 control-label "  >Tel&eacute;fono</label>	
	                                           <div class="col-md-8">
						                        <div class="input-group">                            
                                                	<div class="input-group-addon"><i class="fa fa-phone"></i></div>
	                                                	<input id="txtTelefono" name="txtTelefono" class="form-control" type="text" placeholder="Ingrese un tel&eacute;fono..." value="<?php echo $Cliente->getTelefono();?>">
		                                        </div> 
		                                      	</div>
		                                      </div>	                                           
                                              
                                	</div>
                                   <h4 class="col-md-4 col-md-offset-2">(*) Campos <strong>obligatorios</strong></h4>
						            <div class="col-md-2 col-md-offset-8" >                         
						                <button id="btnsubmitperfil" name="btnsubmitperfil"   class="btn btn-lg btn-success" value="Guardar">Guardar</button>
						            </div>
					             <div class="clearfix"></div>
	                         </form>  
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /#Consulta Cliente -->
	 
 
    <?php require_once ($raiz.'incs/footer4.php');?> 
     
    <script type="text/javascript">
         
    $(document).ready(function() {

     
            $('body').on('click', '.portlet > .portlet-title > .tools > a.reload', function (e) {
                e.preventDefault();
                var el = $(this).closest(".portlet").children(".portlet-body");
                var url = $(this).attr("data-url");
                var error = $(this).attr("data-error-display");
                if (url) {
                    Metronic.blockUI({target: el, iconOnly: true});
                    $.ajax({
                        type: "GET",
                        cache: false,
                        url: url,
                        dataType: "html",
                        success: function(res) 
                        {                        
                            Metronic.unblockUI(el);
                            el.html(res);
                        },
                        error: function(xhr, ajaxOptions, thrownError)
                        {
                            Metronic.unblockUI(el);
                            var msg = 'Error on reloading the content. Please check your connection and try again.';
                            if (error == "toastr" && toastr) {
                                toastr.error(msg);
                            } else if (error == "notific8" && $.notific8) {
                                $.notific8('zindex', 11500);
                                $.notific8(msg, {theme: 'ruby', life: 3000});
                            } else {
                                alert(msg);
                            }
                        }
                    });
                } else {
                    // for demo purpose
                    Metronic.blockUI({target: el, iconOnly: true});
                    window.setTimeout(function () {
                        Metronic.unblockUI(el);
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
        
    });
    
    </script>

</body>

</html>
