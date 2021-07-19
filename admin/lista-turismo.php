<?php
$raiz = "./";
require_once ($raiz.'base.inc.php');
require_once ('sesion.php');

if (!$UsuarioSesion->getSesionBd()){
//	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");


$bd = new BdConexion();

$sqlTurismo = "SELECT a.Id, a.Titulo, a.Descripcion, a.descripcion_corta,DATE_FORMAT(a.Fecha_Edit, '%d-%m-%Y %H:%i:%s') FechaEdit, '' AutorUsername, '' AutorNomApe
FROM sociales a
		WHERE a.Fecha_Baja IS NULL ";

if ($_POST && !empty($_POST['busq'])){
	$postFiltroTxt = $_POST['busq'];
	$sqlTurismo.= " AND a.titulo LIKE :filtro OR a.Descripcion LIKE :filtro ";
}

$bd->query($sqlTurismo);
if (isset($postFiltroTxt)){
	$bd->bind(':filtro', "%$postFiltroTxt%");
}
$bd->execute(); 

$listaTurismo = $bd->getFilas(); 
$totalTurismo = $bd->cantFilas();
$bd = null;

$webTitulo = WEB_TITULO . " - Turismo";

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
						<h1 class="page-header">Noticias <small>Listado de noticias generadas</small></h1>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4 col-sm-4 col-md-4  ">
						<form id="frmbusq" class="" role="search"  method="POST" > 
		                    <div class="form-group">
		                        <div class="input-group">
		                            <span class="input-group-addon"><i class="fa fa-search"></i></span>                                    
		                            <input id="busq" name="busq" type="text" class="form-control" placeholder="Buscar..."  value="">
	                            </div>                              
		                    </div> 
	                	</form>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4 ">
						<a class="btn btn-primary " href="<?php echo URL_ADMIN."alta-turismo.php";?>" title="Crear nuevo">Crear</a>
					</div>
				</div>
				<?php  if(isset($postFiltroTxt)){ ?>
				<div class="row">
					 
					<div class="col-xs-12 col-sm-12 col-md-12 ">
						<div class="alert alert-warning alert-dismisable">
						<a href="<?php echo URL_ADMIN."lista-turismo.php";?>" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a>
						<strong>B&uacute;squeda: </strong><?php echo $postFiltroTxt;?>
						</div>
					</div>
				</div>
				<?php }?>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="panel panel-default">
							<!-- Default panel contents -->
						  	<div class="panel-heading">Novedades</div>
						  	 
							<!-- Inicio tabla  -->
							<div class="table-responsive">
					 			<table class="table table-hover "  >
									<thead>
					                	<tr >
					                    	<th class="text-center" ><label for="checkall"></label><input type="checkbox" id="checkall"></th>
											<th   >Titulo</th>
					                        <th  >Descripci&oacute;n</th>
					                       <th>&Uacute;lt. Edici&oacute;n</th> 		
					                        <th class=""  >Opciones</th>					                         	 
				                    	</tr>
					            	</thead>
					                <tbody>
					                	<?php foreach($listaTurismo as $itemTurismo){ ?>
					                	<tr data-url="<?php echo "";?>">
					                		<td class="text-center" >
					                			<input type="checkbox" class="checkrow" data-id="<?php echo $itemTurismo['Id'];?>">
					                		</td>
					                		<td>
					                			<p class="text-left" style="max-width: 200px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" ><?php echo $itemTurismo['Titulo'];?></p>
					                		</td>
					                		<td>
					                			<p class="text-left" style="max-width: 200px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" ><?php echo $itemTurismo['descripcion_corta'];?></p>
					                		</td>
					                		<td>
	                	 		               	<div>		
					                			<p  style="display:inline-block;">
					                			<?php echo $itemTurismo['FechaEdit'];?>
					                			</p>
					                			<span class="label label-default" style=""><?php echo '';?></span>
					                			 </div>
					                			 
					                		</td>
					                		 
					                		<td class="">
					                			<div class="btn-group btn-group-sm" style="">				                                  
				                                  <a href="<?php echo URL_ADMIN."alta-turismo.php?editar=".$itemTurismo['Id'];?>"  class="btn btn-primary btnedit">Editar</a>
				                                  <a href="#"   class="btn btn-danger btnbaja" data-turismo="<?php echo $itemTurismo['Id'];?>">Borrar</a>				                                  
			                                    </div>
					                		</td>
					                	</tr>
					                	<?php } // fin foreach?>
					                </tbody>
				                </table>		                
			                </div>
			                <!-- /Fin tabla  -->
		                </div>
		                
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div  class="alert alert-success fade in" role="alert">							
							<p>Total de registros: <strong><?php echo $totalTurismo; ?></strong></p>
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
			var turismo = $(this).data('turismo');
			turismoBorrar(turismo);
			window.location.reload();
        });
	       
	});
	
	function turismoBorrar(turismo){
	   
	   $.ajax({
	              url: "admin/funciones/post-turismo-baja.php",
	              type: "POST",
	              data: {                   
	                  'turismo': turismo 
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
