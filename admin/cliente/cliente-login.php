<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
  
if ($ClienteSesion->getSesionBd()){	
	header("Location: $redirCliIndex");
}
 
$webTitulo = WEB_TITULO . " - Clientes - Ingresar";

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
	<div id="portada-cliente" >
		<div class="container">
			<div id="servicios">
					<div class="row">
						<div class="col-md-12 col-sm-12 titulo-encabezado text-center">
				            <h2>Bienvenido a <span>Santa Rita</span> <small></small></h2>
				            <p>Ingres&aacute; a nuestro sistema de consultas.</p>	
				      	</div>
				      	<div><span class="divisor100"></span>	</div>
			      	</div>
		      	</div>
		</div>
	</div>
	<!-- /#portada Cliente-->
	
	<!-- Consulta Cliente -->
	<div id="clientelogin">
		<div class="container ">
			 
			<div class="row">
			 	<div class="col-sm-6 col-md-6">
			 		<?php require_once ('incs/inc-cliente-login.php');?>
			 		 
				</div>
				<div class="col-md-6">
			 		<div class="panel " style="background-color: #ececec; ">
			 			<div class="panel-heading" style="border-color: #FFFFFF; ">
			 				<h4 class="panel-title">¿No estas registrado?</h4>
			 			</div>			 			
	                    <div class="panel-body" >
                    		<div class="col-sm-8">
	                    		<p class=" ">Si todavía no tenés tu cuenta y deseas registrarte, ingresá aquí y completá el formulario con tus datos para iniciar.</p>
	                    	</div>
	                        <div class="pull-right">
	                        	<a href="cliente/registro" class="btn btn-lg btn-success">Registrarme</a>
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
 
             $("#frmlogin").submit(function(ev){
					ev.preventDefault();

					var $btningresar = $("#btningresar");
					$btningresar.button('loading');

					 var cliente = {
				                'nombre' : $("#txtclienteuser").val(),
				                'password' : $("#txtclientepass").val()
				            };
			            
		            var clienteJson = JSON.stringify(cliente);

		            $.blockUI({ message:  '<h1><i class="fa fa-spinner fa-pulse"></i> Ingresando... Por favor aguarde.</h1>'  }); 
		            
		           	clienteLogin({cli: clienteJson}, function(result){

		           		$btningresar.button('reset'); 
						$.unblockUI();
						
		           		$("#resultlogin").html("");  
		           		if (result.ok){                          
		           			window.location.href = "<?php echo $redirCliIndex;?>";  
		           			
			              }else{			              	 
			              	$("#resultlogin").html(result.msj);
			              }
			              
		           	});

		            

		            return;

					
             });
    });

    function clienteLogin(pData, callBack){
        
    	 $.ajax({
             url: "cliente/funciones/cli-post-login.php",
             type: "POST",
             dataType: 'json',  
             data: pData,
             cache: false,
             success: function(r) {                    
				callBack(r);
             },
             error: function(jqxhr ,status, errormsj) {
            	 callBack(r);
             }
         }); 
    }
 
    </script>

</body>

</html>
