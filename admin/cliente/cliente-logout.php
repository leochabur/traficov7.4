<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
 
if ($ClienteSesion->getSesionBd()){	
	$ClienteSesion->sesionFin();
	$clienteLogueado = false;	
}else{
	header("Location: $redirCliLogin");
}
 
$webTitulo = WEB_TITULO . " - Clientes - Fin de sesión";

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
				            <h2>Su sesi&oacute;n ha finalizado</h2>
				            <p>Gracias por utilizar nuestro sistema de consultas.</p>	
				      	</div>
				      	<div><span class="divisor100"></span>	</div>
			      	</div>
		      	</div>
		</div>
	</div>
	<!-- /#portada Cliente-->
	
	<!-- Consulta Cliente -->
	<div id="clientelogout">
		<div class="container ">
			  
			<div class="row">
			 	<div class="col-sm-6 col-md-6">
			 		<?php require_once ('incs/inc-cliente-login.php');?>
				</div>
				<div class="col-md-6">
			 		<div class="panel " style="background-color: #ececec; ">
			 			<div class="panel-heading" style="border-color: #FFFFFF; ">
			 				<h4 class="panel-title">¿Quer&eacute;s dejarnos tu opinion, consulta &oacute; comentario?</h4>
			 			</div>			 			
	                    <div class="panel-body" >
                    		<div class="col-sm-8">
	                    		<p class="text-muted">Mandanos tu mensaje con tu opinion sobre el sistema de consultas.</p>
	                    		<p class="text-muted">Te agradacememos por tu tiempo.</p>
	                    	</div>
	                        <div class="pull-right">
	                        	<a href="cliente/registro" class="btn btn-lg btn-success">Escrib&iacute;nos</a>
	                        </div> 
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

        
             $("#frmlogin").submit(function(ev){
					ev.preventDefault();

					var $btningresar = $("#btningresar");
					$btningresar.button('loading');

					 var cliente = {
				                'nombre' : $("#txtclienteuser").val(),
				                'password' : $("#txtclientepass").val()
				            };
			            
		            var clienteJson = JSON.stringify(cliente);
		            
		           	clienteLogin({cli: clienteJson}, function(result){

		           		$btningresar.button('reset'); 
		           		 
		           		if (result.ok){                          
		           			window.location.reload();  
		           			//$("#resultlogin").html(result.msj);  
		           			
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
             success: function(r) {                    
				callBack(r);
             },
             error: function(jqxhr ,status, errormsj) {
            	 console.log("error cliente login");
             }
         }); 
    }

    function clienteLoginResult(data){

        
        
    }
    
    </script>

</body>

</html>

