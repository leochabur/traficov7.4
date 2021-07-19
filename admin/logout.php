<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');

require_once ('sesion.php');

if ($UsuarioSesion->getSesionBd()){
	$UsuarioSesion->sesionFin();
	//header("Location: $redirLogin");
	 
}



$webTitulo = WEB_TITULO . " - Inicar sesión";

?>
<!DOCTYPE html>
<html>
<head>

    <?php require_once ($raiz.'incs/inc-meta.php');?>
    
    <title><?php echo $webTitulo;?></title>
	<base href="<?php echo RUTA_BASE;?>" target="_top" />
    
    <?php require_once ($raiz.'incs/inc-css.php');?>
            
    <?php require_once ($raiz.'incs/inc-js.php');?>   
 
	
</head>

<body>
 
	<!-- login -->
	<div id="login">
		<div class="container ">
			<div class="row ">
				
				<div class="page-header text-center">
					<img src="imgs/logos/logo_sinfondo_168x60.png" class="pull-left" alt="<?php echo WEB_TITULO;?>" title="<?php echo WEB_TITULO;?>" >						
	                
	            </div>	             
				 
			</div>
			<div class="row ">
				
				<div class="page-header text-center"> 						
	                <h2>Gracias! <span>Su sesión fué finalizada.</span></h2>			
	            </div>	             
				 
			</div>
		</div>
	</div>
	<!-- /#login-->
	 
	 
    <script type="text/javascript">
         
    $(document).ready(function() {

    	$("#frmlogin").submit(function(ev) {
            
            ev.preventDefault();
            var usuario = {
                'username' : $("#txtUser").val(),
                'password' : $("#txtPass").val()
            };
            $.ajax({
                 url: "funciones/postlogin.php",
                 type: "POST",
                 data: {                   
                     usuario: usuario
                 },
                 dataType: 'json',
                 cache: false,
                 success: function(resultado) {                    

                   if (resultado.procesook){                            
                         window.location.reload();
                   }else{
                         $("#resultlogin").html(resultado.result.error);                         
                   }
                 },
                 error: function(resultado) {
                     $("#resultlogin").html("No se pudo realizar la operación.");   
                 }
             }); 
              
         });
            
    });
    
    </script>

</body>

</html>
