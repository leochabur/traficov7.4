<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
require_once ('sesion.php');

if (!$UsuarioSesion->getSesionBd()){
	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");

$bd = new BdConexion();

$sql  = " "; 
$bd->query($sql);
try{
	$bd->execute();
} catch (Exception $ex){
	
}
$bd = null;

$webTitulo = WEB_TITULO . " - Perfil";

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

    <div id="contenedor">
    	<?php require_once ('incs/inc-admin-nav.php');?>
     
		<!-- Admin -->
		<div id="adminpanel">
			<div class="container-fluid ">
           	
           		<div class="row">
				 	<div class="col-xs-12 col-sm-12 col-md-12">
						<h1 class="page-header">Perfil <small>Usuario <span class="label label-primary">@ <?php echo $Usuario->getUser();?></span></small> </h1>				 		
					</div>
				</div>
				
        		<div class="row">    
            
		            <div class="col-lg-12 alert alert-warning">     
		                 Complete los campos a continuaci&oacute;n y presione el bot&oacute;n <strong>Guardar</strong> al final del formulario.
		            </div>
			 
		            <!-- Si es administrador se muestra el formulario de carga -->                
		            <ul id="tabs" class="nav nav-tabs " data-tabs="tabs">
		                <li class="active"><a href="perfil/#config" data-toggle="tab"><strong>Configuraci&oacute;n</strong></a></li>
		                
		            </ul>
		
		            <div id="contenido" class="tab-content" style="margin-top: 15px;">
 			 
	                    <div id="config" class="tab-pane active">
	                        <form id="frmusuario" class="form-horizontal updateform" role="form" method="POST"   >
	                            <div class="col-lg-6">     
	                                <div class="panel panel-primary">
	                                    <div class="panel-heading">
	                                        <h3 class="panel-title">* Campos <strong>obligatorios</strong></h3>
	                                    </div>
	
	                                    <div class="panel-body">
											 <div class="col-md-12">   
											 	<div class="form-group">			                                        			                                         
		                                        	 
			                                      </div>
			                                     <div class="form-group">
			                                        <label for="txtNombre" class=" ">Nombre *</label>			                                        
		                                        	<input type="text" class="form-control" id="txtNombre" name="txtNombre" placeholder="Ingrese su nombre..." value="<?php echo $Usuario->getNombre();?>">
			                                      </div>
			                                      <div class="form-group">
			                                        <label for="txtApellido"  >Apellido *</label>			                                          
		                                         	<input type="text" class="form-control" id="txtApellido" name="txtApellido" placeholder="Ingrese su apellido..." value="<?php echo $Usuario->getApellido();?>">			                                          
			                                      </div>
			
			                                      <div class="form-group">
			                                          <label for="txtEmail"  >Email *</label>			                                           
		                                              <div class="input-group">                            
		                                                <div class="input-group-addon">@</div>
		                                                	<input id="txtEmail" name="txtEmail" class="form-control" type="email" placeholder="Ingrese una cuenta de correo v&aacute;lida..." value="<?php echo $Usuario->getEmail();?>">
		                                              </div> 
			                                      </div>                    
			                                      
	                                		 </div>
	                                    </div>
	                                    <div class="panel-footer"></div>
	                                </div> 
	                            </div>
	
	                            <div class="col-lg-6">   
	
	                                <div class="panel panel-info">
	                                    <div class="panel-heading">
	                                        <h3 class="panel-title">Campos opcionales</h3>
	                                    </div>
	                                    <div class="panel-body"> 
	                                    	<div class="col-md-12">   
	                                          <div class="form-group">
	                                            <label for="txtObs"  >Observaciones</label>	                                             
                                            	<textarea class="form-control" id="txtObs" rows="3" name="txtObs" placeholder="Ingrese alg&uacute;n comentario..."><?php echo ""; ?></textarea>
                                            	</div>
	                                    	</div>     
	                                    </div>
	                                    <div class="panel-footer"></div>
	                                </div> 
	                            </div>
	                         </form>   
	                        
	                    </div> <!-- /.div config -->
                 
                        
            		</div> <!-- /.div contenido-->   
            		<div class="clearfix"></div>
		            <div class="col-md-12" >                         
		                <button id="btnsubmitperfil" name="btnsubmitperfil"   class="btn btn-lg btn-success" value="Guardar">Guardar</button>         
		                
		            </div>
        		</div> <!-- /.col-row -->    
         
    		</div><!-- /.container -->
		</div><!-- /.container -->
	</div><!-- /.container -->
	
    <script type="text/javascript">
         
        
        $(document).ready(function() {
   
            $('.ephoto-upload').change(function(){
                previewURL(this);   
                $("#frmavatarupload").submit();
           }); 
            
           $("#frmusuario-alta").submit(function(ev){ 
               
               ev.preventDefault();
               
               var $email = $("#txtEmail");
               var $username = $("#txtUsername");
               var $nombre = $("#txtNombre");
               var $apellido = $("#txtApellido");
               var $pass1 = $("#txtPass1");
               var $pass2 = $("#txtPass2");
               
               if ($email.val() === "") 
               {
                   $email.focus();
                   return false;               
                }
                if ($username.val() === "") 
               {
                    $username.focus();
                   return false;               
                }
             
                this.submit();
            
            });
 
           
    });
     
      function previewURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imgavatar').attr('src', e.target.result);
                    //$('#frmThumb').css("background", "url(" + e.target.result +")"  + " ");    
                };

                reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
    
</body>

</html>
