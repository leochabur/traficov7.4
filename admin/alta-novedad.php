<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
require_once ('sesion.php');

if (!$UsuarioSesion->getSesionBd()){
	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");

$webTitulo = WEB_TITULO . " - Nueva Novedad";
 
if (isset($_GET['editar']) && !empty($_GET['editar'])){
	$idEdit = $_GET['editar'];
	$bd = new BdConexion();
	$query = "SELECT Id, Titulo, Descripcion, FechaAlta, FechaEdit, IdUsuario FROM novedad WHERE (Id = :id) AND (FechaBaja IS NULL); ";
	$bd->query($query);
	$bd->bind(':id', $idEdit);
	$bd->execute();
	$itemNovedadEdit =$bd->getFila();
	$bd = null;
	if (!empty($itemNovedadEdit)){

		$webTitulo = WEB_TITULO . " - Editar Novedad";

	}
}


if (isset($_POST) && $_POST){

	$camposOk = true;
	$postEnviado = true;
	$mensajePost = "Complete los campos obligatorios... ";

	if (!isset($_POST['txttitulo']) || ($_POST['txttitulo'] === "")){
		$camposOk = false;
		$mensajePost .= "Debe ingresar el titulo... ";
	}else{
		$postTitulo 	= $_POST['txttitulo'];
	}
 
	$postDescrip 	= $_POST['txtdescrip'];
	 
	if ($camposOk){

		$bd = new BdConexion();
	 
		if (isset($itemNovedadEdit)){
				
			$sqlUpdate = "UPDATE novedad
					SET Titulo = :titulo, Descripcion = :descrip, FechaEdit = NOW()
					WHERE Id = :id; ";
			$bd->query($sqlUpdate);
			$bd->bind(':id', $itemNovedadEdit['Id']);
				
		}else{
			$sqlAlta = "INSERT INTO novedad (Titulo, Descripcion, FechaAlta, FechaEdit, IdUsuario) VALUES (:titulo, :descrip , NOW(), NOW(), :idusuario); ";
			$bd->query($sqlAlta);
			$bd->bind(':idusuario', $Usuario->getUserId());
		}
		 
		$bd->bind(':titulo', $postTitulo);
		$bd->bind(':descrip', $postDescrip);		  
		$bd->execute();
		
		$bd = null;
		$postOk = true;
		$mensajePost = "Registro guardado!";
		header("Location: " . URL_ADMIN."lista-novedades.php");
	}

}

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
						<h1 class="page-header">Novedades <small>Crear nueva</small></h1>				 		
					</div>
				</div>
				<div class="row">
					<form role="form"  id="frmnovedad"  method="POST" >
						<div class="col-md-6">
							<h3>Complete los campos</h3>
                    	 
                            <div class="form-group">
                                <label>Titulo</label>
                                <input class="form-control" id="txttitulo"  name="txttitulo" placeholder="Ingrese el titulo de la novedad..." value="<?php if(isset($itemNovedadEdit)) {echo $itemNovedadEdit['Titulo'];}?>">
                                
                            </div>
                            <div class="form-group">
                                <label>Descripci&oacute;n</label>
                                <textarea class="form-control" id="txtdescrip"  name="txtdescrip" rows="5" placeholder="Ingrese la descripci&oacute;n de la novedad..."><?php if(isset($itemNovedadEdit)) {echo $itemNovedadEdit['Descripcion'];}?></textarea>
                            </div>
                             
						</div>
						 
						<div class="col-md-12 ">
                        	<button id="btnsendform" name="btnsendform" type="submit" data-loading-text="Guardando..." class="btn btn-lg btn-success " autocomplete="off">Guardar</button>                                        
                        </div>
					</form>
                </div>
                <!-- /.row -->
			</div>
		</div>
		<!-- /#adminpanel-->
		
	</div> 
	<!-- /#Contenedor-->
	
    <script type="text/javascript">
         
    $(document).ready(function() {
            
    	$("#frmnovedad").submit(function(ev){
			//ev.preventDefault();
			 
			$inputTitulo = $("#txttitulo");
			$inputTitulo.closest(".form-group").removeClass("has-error");
			 
			if ($inputTitulo.val() === ""){
				$inputTitulo.closest(".form-group").addClass("has-error");
				return false;
			}

			var $btnsend = $("#btnsendform").button('loading');
			 
			return true;
        });
            
    });
    
    </script>

</body>

</html>
