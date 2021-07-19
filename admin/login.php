<?php
$raiz = "./";
require_once ($raiz.'base.inc.php');

require_once ('sesion.php');


if ($UsuarioSesion->getSesionBd()){	
	header("Location: $redirIndex");
}


$webTitulo = WEB_TITULO . " - Inicar sesión";

?>
<!DOCTYPE html>
<html>
<head>

   
    <?php require_once ($raiz.'incs/inc-meta.php');?>
    
    <title><?php echo $webTitulo;?></title>
	<base href="<?php echo RUTA_BASE;?>" target="_top" />
    
    <?php require_once ('incs/inc-admin-css.php');?> 
            
    <?php require_once ('incs/inc-admin-js.php');?>   
 
	
</head>

<body>
 
	<!-- login -->
	<div id="login">
		<div class="container ">
			<div class="row ">
				
				<div class="page-header text-center">
					<img src="imgs/logos/logo_sinfondo_168x60.png" class="pull-left" alt="<?php echo WEB_TITULO;?>" title="<?php echo WEB_TITULO;?>" >						
	                <h2>Bienvenido a<span>   Santa Rita</span></h2>			
	            </div>	             
				<div class="col-xs-12 col-sm-4 col-sm-offset-3 col-md-4 col-md-offset-3 col-lg-4 col-lg-offset-4"> 
	                <div class="panel panel-default">
	                    <div class="panel-body">
	                        <form id="frmlogin" class="form-horizontal" role="form">
	                            <div class="form-group">                                 
	                                <div class="col-sm-10">
	                                	<input type="text" class="form-control" id="txtUser" placeholder="Usuario..." >
                                	</div>
	                              </div>
	                            <div class="form-group">                             
	                                <div class="col-sm-6">
	                                	<input type="password" class="form-control" id="txtPass" placeholder="Contrase&ntilde;a..." >
                                	</div>
	                                <button type="submit" class="btn btn-primary">Iniciar sesi&oacute;n</button>
	                            </div>  
	                            <div class="form-group text-center">  
									<a href='recuperar-clave'> ¿Olvidaste tu contrase&ntilde;a?</a> 
	                            </div> 
	                        </form>
	                         <output id="resultlogin" class="text text-muted text-center"></output> 
	                    </div>
	                </div>
	                 
	            </div>
            
			</div>
		</div>
	</div>
	<!-- /#login-->
	 
	 
    <script type="text/javascript">
         
    $(document).ready(function() {

    	$("#txtUser").focus();
    	
    	$("#frmlogin").submit(function(ev) {
            
            ev.preventDefault();
            var usuario = {
                'username' : $("#txtUser").val(),
                'password' : $("#txtPass").val()
            };
            $.ajax({
                 url: "/admin/funciones/post-login.php",
                 type: "POST",
                 data: {                   
                     usuario: usuario
                 },
                 dataType: 'json',
                 cache: false,
                 success: function(resultado) {                    

                   if (resultado.ok){                            
                         window.location.reload();
                   }else{
                         $("#resultlogin").html(resultado.error);                         
                   }
                 },
                 error: function(resultado) {
                        alert(resultado.error);
                     $("#resultlogin").html("No se pudo realizar la operación.");   
                 }
             }); 
              
         });
            
    });
    
    </script>

</body>

</html>
