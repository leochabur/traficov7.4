<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
  
if ($ClienteSesion->getSesionBd()){	
	header("Location: $redirCliIndex");
}
 
$webTitulo = WEB_TITULO . " - Cliente - Recuperar clave";

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
	<div id="portada-cliente" >
		<div class="container">
			<div id="servicios">
					<div class="row">
						<div class="col-md-12 col-sm-12 titulo-encabezado text-center">
				            <h2>Recuper&aacute; tu clave</h2>
				            <p>Si no record&aacute;s tu clave complet&aacute; los datos solicitados y te la enviamos a tu direcci&oacute;n de correo.</p>	
				      	</div>
				      	<div><span class="divisor100"></span>	</div>
			      	</div>
		      	</div>
		</div>
	</div>
	
	<!-- Consulta Cliente -->
	<div id="clientelogin">
		<div class="container ">
			 
			<div class="row">
			 	<div class="col-sm-6 col-md-6">
			 		<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-shield fa-fw"></i>Ingres&aacute; los datos de tu cuenta
							</div>
						 
						</div>
			 			<div class="portlet-body">
			 				<div class="col-lg-12 ">     
			                 	<p class="text-muted">Completa tu direcci&oacute;n de correo electronico.</p>
			            	</div>
							<form id="frmenvioclave" class="form-horizontal" role="form">
	                            
	                            <div class="form-group">           
                                    <label for="txtclienteemail" class="control-label col-sm-2">Email:</label>
	                                <div class="col-sm-8">
	                                	<input type="email" class="form-control" id="txtclienteemail" placeholder="Email..." required >
                                	</div>
	                            </div>  
	                              
	                            <div class="col-sm-12 ">	                            
	                            	<button id="btnenviarclave" type="submit" class="btn btn-lg btn-primary pull-right" data-loading-text="Enviando...">Enviar</button>
	                            </div>
	                        </form>
	                        <div class="col-sm-12 ">	 
	                         	<output id="resultenvioclave" class="text-muted text-center"></output> 
	                         </div>
	                         <div class="clearfix"></div>
						</div>
			  
	                </div>
				</div>
				<div class="col-md-6">
					<div class="col-sm-12">
				 		<div class="panel " style="background-color: #ececec; ">
				 			<div class="panel-heading" style="border-color: #FFFFFF; ">
				 				<h4 class="panel-title">¿No estas registrado?</h4>
				 			</div>			 			
		                    <div class="panel-body" >
	                    		<div class="col-sm-8">
		                    		<p class=" ">Si todavía no tenés tu cuenta y deseas registrarte, ingresá aquí y completá el formulario con tus datos para iniciar.</p>
		                    	</div>
		                        <div class="pull-right">
		                        	<a href="<?php echo URL_CLIENTE. "registro";?>" class="btn btn-lg btn-success">Registrarme</a>
		                        </div> 
		                    </div>
		                </div>
	                </div>
	                <div class="col-sm-12">
		                <div class="panel panel-primary" style="background-color: #ececec; ">
				 			<div class="panel-heading" style="border-color: #FFFFFF; ">
				 				<h4 class="panel-title">Ingres&aacute;r al sistema</h4>
				 			</div>			 			
		                    <div class="panel-body" >
	                    		<div class="col-sm-8">
		                    		<p class=" ">Si ya estas registrado presiona el boton Ingresar.</p>
		                    	</div>
		                        <div class="pull-right">
		                        	<a href="<?php echo URL_CLIENTE. "login";?>" class="btn btn-lg btn-success">Ingresar</a>
		                        </div> 
		                    </div>
		                </div>
	                </div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<!-- /#Consulta Cliente -->
	 
 
    <?php require_once ($raiz.'incs/footer4.php');?> 
     
    <script type="text/javascript">
         
    $(document).ready(function() {

        
             $("#frmenvioclave").submit(function(ev){
					ev.preventDefault();

					var $btnenviar = $("#btnenviarclave");
					$btnenviar.button('loading');

					 var cliente = { 
				                'email' : $("#txtclienteemail").val()
				            };
			            
		            var clienteJson = JSON.stringify(cliente);
		            
		            clienteEnviarClave({cli: clienteJson}, function(result){

		           		$btnenviar.button('reset'); 
		           		 
		           		if (result.ok){                         
		           			 
		           			 $("#resultenvioclave").html(result.msj);  
		           			
			              }else{			              	 
			              	$("#resultenvioclave").html(result.msj);
			              }
			              
		           	});

		            

		            return;

					
             });
    });

    function clienteEnviarClave(pData, callBack){
        
    	 $.ajax({
             url: "cliente/funciones/cli-post-enviarclave.php",
             type: "POST",
             dataType: 'json',  
             data: pData,
             success: function(r) {                    
				callBack(r);
             },
             error: function(jqxhr ,status, errormsj) {
            	 console.log("error al enviar el mensaje");
             }
         }); 
    }
 
    </script>

</body>

</html>
