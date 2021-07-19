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

$sqlNovedades = "SELECT a.Id,a.Titulo, a.Descripcion, DATE_FORMAT(a.FechaEdit, '%d-%m-%Y %H:%i:%s') FechaEdit, b.Username AutorUsername, CONCAT(b.Nombre, ' ', b.Apellido) AutorNomApe 
		FROM novedad a
		LEFT JOIN usuario b ON(a.IdUsuario = b.Id)
		WHERE a.FechaBaja IS NULL ;";

 
$bd->query($sqlNovedades);
$bd->execute();
$listaNovedades = $bd->getFilas();
$totalNovedades = $bd->cantFilas();
$bd = null;

$webTitulo = WEB_TITULO . " - Novedades";

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
						<h1 class="page-header">Novedades <small>Listado de novedades</small></h1>				 		
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4 col-sm-4 col-md-4  ">
						<form id="frmbusq" class="" role="search"  method="POST" > 
		                    <div class="form-group">
		                        <div class="input-group">
		                            <span class="input-group-addon"><i class="fa fa-search"></i></span>                                    
		                            <input id="busq" name="busq" type="text" class="form-control" placeholder="Buscar..."  value="<?php if(isset($postFiltro)): echo $postFiltro; endif;?>">
	                            </div>                              
		                    </div> 
	                	</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 ">
						<a class="btn btn-primary " href="<?php echo URL_ADMIN."alta-novedad.php";?>" title="Crear nueva novedad">Crear</a>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<!-- Inicio tabla  -->
						<div class="table-responsive">
				 			<table class="table table-bordered table-hover "  >
								<thead>
				                	<tr>
				                    	<th class="text-center"><label for="checkall"></label><input type="checkbox" id="checkall"></th>
										<th>Titulo</th>
				                        <th>Descripci&oacute;n</th>			                                                 	 
				                        <th>&Uacute;lt. Edici&oacute;n</th> 			                        
				                        <th class="">Opciones</th>					                         	 
			                    	</tr>
				            	</thead>
				                <tbody>
				                	<?php foreach($listaNovedades as $itemNovedad){ ?>
				                	<tr data-url="<?php echo "";?>">
				                		<td class="text-center" >
				                			<input type="checkbox" class="checkrow" data-id="<?php echo $itemNovedad['Id'];?>">
				                		</td>
				                		<td>
				                			<p class="text-left" style="max-width: 200px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" ><?php echo $itemNovedad['Titulo'];?></p>
				                		</td>
				                		<td>
				                			<p class="text-left" style="max-width: 150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" ><?php echo $itemNovedad['Descripcion'];?></p>
				                		</td>
				                		 
				                		<td>
	                	 		               	<div>		
					                			<p  style="display:inline-block;">
					                			<?php echo $itemNovedad['FechaEdit'];?>
					                			</p>
					                			<span class="label label-default" style=""><?php echo $itemNovedad['AutorNomApe'];?></span>
					                			 </div>
					                			 
					                		</td>
				                		 
				                		<td class="">
				                			 
		                                    <div class="btn-group btn-group-sm" style="">				                                  
				                                  <a href="<?php echo URL_ADMIN."alta-novedad.php?editar=".$itemNovedad['Id'];?>"  class="btn btn-primary btnedit">Editar</a>
				                                  <a href="#"   class="btn btn-danger btnbaja" data-novedad="<?php echo $itemNovedad['Id'];?>">Borrar</a>				                                  
			                                    </div>
				                		</td>
				                	</tr>
				                	<?php } // fin foreach?>
				                </tbody>
			                </table>		                
		                </div>
		                <!-- /Fin tabla  -->
		                
					</div>
                    
	                <div class="col-xs-12 col-sm-12 col-md-12">
						<div  class="alert alert-success fade in" role="alert">							
							<p>Total de registros: <strong><?php echo $totalNovedades; ?></strong></p>
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
            
             $(".btnbaja").click(function(ev){
				ev.preventDefault();
				var novedad = $(this).data('novedad');
				novedadBorrar(novedad);
				window.location.reload();
             });
            
    });
    
    function novedadBorrar(novedad){
        
        $.ajax({
                   url: "admin/funciones/post-novedad-baja.php",
                   type: "POST",
                   data: {                   
                       'novedad': novedad 
                   },
                   dataType: 'json',
                   cache: false,
                   success: function(resultado) {     
                       if (!resultado.ok){
						alert(resultado.error);
                       }   
                      return resultado.ok;
                   },
                   error: function(resultado) {
                	   return false;
                   }
               }); 
	}
    </script>

</body>

</html>
