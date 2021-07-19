<?php
$raiz = "./";
require_once ($raiz.'base.inc.php');
require_once ('sesion.php');

if (!isset($Usuario) || empty($Usuario)){
	header("Location: $redirLogin");
	//echo $UsuarioSesion->getError();
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");

$bd = new BdConexion();

$sqlNovedades = "SELECT COUNT(*) total FROM novedad a WHERE a.FechaBaja IS NULL ;"; 
$bd->query($sqlNovedades);
$bd->execute();
$totalNovedades = $bd->getFila(); 
$sqlTurismo = "SELECT COUNT(*) total FROM turismo a WHERE a.FechaBaja IS NULL ;";
$bd->query($sqlTurismo);
$bd->execute();
$totalTurismo = $bd->getFila();

$bd = null;

$webTitulo = WEB_TITULO . " - Administración";

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
						<h1 class="page-header">Bienvenido a Santa Rita <small>Panel de administraci&oacute;n</small></h1>				 		
					</div>
				</div>
				<div class="row">
					<div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-bus fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $totalTurismo['total'];?></div>
                                        <div>
                                        	Paquetes Turismo
                                        </div>
                                    </div>
                                </div>
                            </div>
                             
                            <div class="panel-footer">
                                    <a class="pull-left" href="<?php echo URL_ADMIN. "lista-turismo.php";?>">Ver lista</a>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                            </div>
                            
                        </div>
                    </div>
                     
					<div class="col-lg-3 col-md-6">
                        <div class="panel panel-verde">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-bell fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"><?php echo $totalNovedades['total'];?></div>
                                        <div>Novedades</div>
                                    </div>
                                </div>
                            </div>
                             
                            <div class="panel-footer">
                                    <a class="pull-left" href="<?php echo URL_ADMIN. "lista-novedades.php";?>">Ver lista</a>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!-- /.row -->
			</div>
		</div>
		<!-- /#adminpanel-->
		
	</div> 
	<!-- /#Contenedor-->
	
    <script type="text/javascript">
         
    $(document).ready(function() {
            
             
            
    });
    
    </script>

</body>

</html>
