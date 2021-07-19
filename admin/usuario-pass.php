<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
require_once ('sesion.php');

if (!$UsuarioSesion->getSesionBd()){
	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
// Seguridad
require_once ($raiz.RUTA_LIB . "clases/Seguridad.clase.php");

if (isset($_POST) && $_POST){

	 
	$resultpostpass = false;
	 
	if (isset($_POST['txtClaveOriginal']) && ($_POST['txtClaveOriginal'] !== "")){
		
		$bd = new BdConexion();
		$sql  = "SELECT Pass FROM usuario WHERE Id = :id ; ";
		$bd->query($sql); 
		$bd->bind(":id", $Usuario->getUserId());
		try{
			$bd->execute();
			$userPass = $bd->getFila()['Pass'];
		} catch (Exception $ex){ 
			$resultpostpass = false;
		}
		
		// valida hashed password
		$postpassorig = $_POST['txtClaveOriginal'];  
		if (Seguridad::validaPasswordHash($postpassorig,$userPass)){	
	
			$postpassnueva1 = $_POST['txtNuevaClave1'];
			$postpassnueva2 = $_POST['txtNuevaClave2'];
				
			// valido las nuevas password que coincidan 
			if ( ($postpassnueva1 === "") || ($postpassnueva2 === "") || ($postpassnueva1 !== $postpassnueva2)){
				$msjpostpass = "Las contraseñas nuevas ingresadas no coinciden.";
			 
			}else{
				// creo la password
				$passhash = Seguridad::hash_password($postpassnueva1); 
				$sql  = "UPDATE usuario SET Pass =:password WHERE Id = :id ; ";
				$bd->query($sql);
				$bd->bind(":password", $passhash);
				$bd->bind(":id", $Usuario->getUserId());
				try{
					$bd->execute();
					$resultpostpass = true;
					$msjpostpass = "Su contraseña se actualizó correctamente!.";
				} catch (Exception $ex){
					$msjpostpass = "Sus datos no se pudieron actualizar.";
					$resultpostpass = false;
				}
					
				
			}			
			 
		}else{			 
			$msjpostpass = "La contraseña actual no coincide.";
		}
		
		$bd = null;
		
	} else{
			 
			$msjpostpass = "No ingresaste tu contraseña actual.";
		}
 
}

$webTitulo = WEB_TITULO . " - Cambio de contraseña";

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
	             	<div  class="alert <?php if(isset($resultpostpass)){ echo (!($resultpostpass) ? "alert-danger" : "alert-success" ); }?> <?php echo (!isset($resultpostpass) ? "hidden" : "" );?>">                         
                            <?php echo (isset($msjpostpass)) ? $msjpostpass : ""; ;?>
                    </div> 
	                <div class="col-lg-12 alert alert-warning">     
	                   Ingrese su contrase&ntilde;a actual y luego ingrese una nueva. Presionar <strong>Guardar</strong> al final del formulario para terminar.
	                </div>
	                
	                <div class="col-lg-6">  
	                     
	                    <form id="frmusuariopass" class="form-horizontal" role="form" method="POST">
	                         
	                        <div class="panel panel-info">
	                            <div class="panel-heading">
	                              <h3 class="panel-title">Seguridad</h3>
	                            </div>
	                            <div class="panel-body">
	                                 
	                              <div class="form-group"> 
	                                <label for="txtClaveOriginal" class="col-sm-3 control-label">Actual *</label>
	                                <div class="col-sm-8 input-group">
	                                     <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>                                    
	                                    <input type="password" class="form-control" id="txtClaveOriginal"  name="txtClaveOriginal" placeholder="Ingrese su clave actual..." value="">
	                                </div>
	                              </div>
	                              <div class="form-group">
	                                <label for="txtNuevaClave1" class="col-sm-3 control-label">Nueva *</label>
	                                 <div class="col-sm-8 input-group">
	                                       <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span> 
	                                     <input type="password" class="form-control" id="txtNuevaClave1" name="txtNuevaClave1" placeholder="Ingrese la clave nueva..." value="">
	                                 </div>
	                              </div>
	                                <div class="form-group">
	                                <label for="txtNuevaClave2" class="col-sm-3 control-label">Confirmar *</label>
	                                 <div class="col-sm-8 input-group">
	                                       <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span> 
	                                       <input type="password" class="form-control" id="txtNuevaClave2" name="txtNuevaClave2" placeholder="Re ingrese la clave nueva..." value="">
	                                 </div>
	                              </div>
	                                
	                                <div class="col-md-12" >                         
	                                    <button id="btnsubmitpass" name="btnsubmitpass" type="submit" class="btn btn-lg btn-success" value="Guardar">Guardar</button>         
	                                </div>
	                            </div>
	                             <div class="panel-footer"></div>
	                         </div>
	                        
	                        
	                     </form>   
	                    
	                </div>
	                     
	                
	                
	            </div> <!-- /.col-row -->   
         	</div><!-- /.container -->    
    	</div><!-- /.container -->	        
    </div><!-- /.container -->
     
    <script type="text/javascript">
        
        $(document).ready(function() {
  

    });
     
    </script>
    
</body>

</html>
