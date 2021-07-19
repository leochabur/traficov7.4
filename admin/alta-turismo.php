<?php
$raiz = "./";
require_once ($raiz.'base.inc.php');
require_once ('funciones/general.php');

require_once ('sesion.php');

if (!$UsuarioSesion->getSesionBd()){
//	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");

$webTitulo = WEB_TITULO . " - Nuevo Paquete Turismo";

if (isset($_GET['editar']) && !empty($_GET['editar'])){
	$idEdit = $_GET['editar'];
	$bd = new BdConexion();
	$query = "SELECT Id, Titulo, Descripcion, descripcion_corta FROM sociales WHERE (Id = :id) AND (Fecha_Baja IS NULL); ";
	$bd->query($query);
	$bd->bind(':id', $idEdit); 
	$bd->execute();
	$itemTurismoEdit =$bd->getFila(); 
	$bd = null; 
	if (!empty($itemTurismoEdit)){

		$webTitulo = WEB_TITULO . " - Editar Paquete Turismo";
		
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
	
	if (!isset($_POST['txtdescripcioncorta']) || ($_POST['txtdescripcioncorta'] === "")){
		$camposOk = false;
		$mensajePost .= "Debe ingresar la descripcion corta... ";
	}else{
		$postDescripcionCorta 	= $_POST['txtdescripcioncorta'];
	}
	
	$postNombreAmigable = sanear_string($postTitulo);
	$postDescripcion 	= $_POST['txtdescripcion'];
	
	if ($camposOk){
		
		$bd = new BdConexion();
		 
		if (isset($itemTurismoEdit)){
			
			$sqlUpdate = "UPDATE sociales
					SET Titulo = :titulo, Titulo_Amigable =:tituloamigable, Descripcion = :descrip, descripcion_corta = :desccorta , Fecha_Edit = NOW()
					WHERE Id = :id; ";
			$bd->query($sqlUpdate);			
			$bd->bind(':id', $itemTurismoEdit['Id']);			 
			
		}else{
			$sqlAlta = "INSERT INTO sociales (Titulo, Titulo_Amigable, Descripcion, Fecha, Lugar, Fecha_Alta, Fecha_Edit, Habilitado, descripcion_corta)
					VALUES (:titulo,:tituloamigable,  :descrip, NOW(), '', NOW(), NOW(),1, :desccorta); ";
			$bd->query($sqlAlta); 
		//	$bd->bind(':idusuario', $Usuario->getUserId());
		}
		
		$bd->bind(':titulo', $postTitulo);
		$bd->bind(':tituloamigable', $postNombreAmigable);
		$bd->bind(':descrip', $postDescripcion);
		$bd->bind(':desccorta', $postDescripcionCorta);
		$bd->execute();
		
		$bd = null;
		$postOk = true;
		 
		$mensajePost .= "Registro guardado!" ;
		header("Location: " . URL_ADMIN."lista-turismo.php");
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
            
	<link href="admin/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet"/>
	<link href="admin/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
	<link href="admin/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>

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
					 	<h1 class="page-header">Noticias
					 	
					 		<?php if (!empty($itemTurismoEdit)) { ?>
					 		<small>Editar noticia</small>
					 		<?php }else{ ?>
					 		<small>Crear noticia</small>
					 		<?php }?>
				 		 </h1>
					</div>
				</div>
				<div class="row">
					
					<?php if (isset($camposOk) && !$camposOk){ ?>
					
					<div class="col-md-12">
						<div  class="alert alert-danger fade in" role="alert">							
							<strong><?php echo $mensajePost; ?></strong>
						</div>     
					</div>
					
					<?php }?>
					
					 <!-- Si es administrador se muestra el formulario de carga -->                
		            <ul id="tabs" class="nav nav-tabs " data-tabs="tabs">
		                <li class="active"><a href="#datos" data-toggle="tab"><strong>Principal</strong></a></li>
		                
		                <li class="<?php if (!isset($itemTurismoEdit)) { echo "disabled";}?>"><a href="#imagenes"  data-toggle="tab"   ><strong>Im&aacute;genes</strong></a></li>
		               
		                 
		            </ul>
							
					<div id="contenido" class="tab-content" >
					
						<div id="datos"  class="tab-pane active">
							<form role="form" id="frmturismo"  method="POST" >
								<div class="col-md-12">
									<h3>Complete los campos</h3>
		                            <div class="form-group">
		                            	<label class="control-label" for="txtprecio">Titulo</label>
		                            	<div class="input-group">
			                                <input id="txttitulo"  name="txttitulo" class="form-control" placeholder="Ingrese el titulo..." value="<?php if(isset($itemTurismoEdit)) {echo $itemTurismoEdit['Titulo'];}?>">
		                                </div>
		                            </div>
									
		                            <div class="form-group">
		                                <label class="control-label" for="txtnombre">Descripci&oacute;n corta</label>
		                                <input id="txtdescripcioncorta"  name="txtdescripcioncorta" class="form-control" placeholder="Ingrese la descripci&oacute;n corta..." value="<?php if(isset($itemTurismoEdit)) {echo $itemTurismoEdit['descripcion_corta']; }?>">
		                                
		                            </div>

		                            <div class="form-group">
		                                <label class="control-label" for="txtdescrip">Descripci&oacute;n</label>
		                                <textarea id="txtdescripcion"  name="txtdescripcion" class="form-control" rows="10" placeholder="Ingrese la descripci&oacute;n..."><?php if(isset($itemTurismoEdit)) {echo $itemTurismoEdit['Descripcion'];}?></textarea>
		                            </div>
								
								</div>
								<div class="col-md-12 ">
		                        	<button id="btnsendform" name="btnsendform" type="submit" data-loading-text="Guardando..." class="btn btn-lg btn-success " autocomplete="off">Guardar</button>
		                        	<a href="<?php echo URL_ADMIN."lista-turismo.php";?>" id="btnback" name="btnback" class="btn btn-lg btn-warning" >Volver</a>                                        
		                        </div>
							</form>	
						</div>
						
						<div id="imagenes"  class="tab-pane">
							<form role="form" id="frmimagenes"  method="POST" enctype="multipart/form-data">
								<input type="hidden" name="idTurismo" value="<?php if(isset($itemTurismoEdit)) {echo $itemTurismoEdit["Id"];}?>">
								<div class="col-md-12">
							    	<h3>Agregue las im&aacute;genes</h3>
		                            	 <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
										<div class="row fileupload-buttonbar">
											<div class="col-lg-7">
												<!-- The fileinput-button span is used to style the file input field as button -->
												<span class="btn green fileinput-button">
												<i class="fa fa-plus"></i>
												<span>
												Agregar im&aacute;n... </span>
												<input type="file" name="files[]" multiple="">
												</span>
												<button type="submit" class="btn blue start">
												<i class="fa fa-upload"></i>
												<span>
												Subir </span>
												</button>
												<button type="reset" class="btn warning cancel">
												<i class="fa fa-ban-circle"></i>
												<span>
												Cancelar </span>
												</button>
												<button type="button" class="btn red delete">
												<i class="fa fa-trash"></i>
												<span>
												Borrar </span>
												</button>
												<input type="checkbox" class="toggle">
												<!-- The global file processing state -->
												<span class="fileupload-process">
												</span>
											</div>
											<!-- The global progress information -->
											<div class="col-lg-5 fileupload-progress fade">
												<!-- The global progress bar -->
												<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
													<div class="progress-bar progress-bar-success" style="width:0%;">
													</div>
												</div>
												<!-- The extended global progress information -->
												<div class="progress-extended">
													 &nbsp;
												</div>
											</div>
										</div>
										<!-- The table listing the files available for upload/download -->
										<table role="presentation" class="table table-striped clearfix">
										<tbody class="files">
										</tbody>
										</table>                       
										                
										<div class="panel panel-success">
											<div class="panel-heading">
												<h3 class="panel-title">Aclaraciones</h3>
											</div>
											<div class="panel-body">
												<ul>
													<li>
														 El tama&ntilde;o m&aacute;ximo de las im&aacute;nes para subir es de <strong>5 MB</strong>.
													</li>
													<li>
														 El formato de las im&aacute;nes debe ser (<strong>JPG, GIF, PNG</strong>).
													</li> 
												</ul>
											</div>
										</div>
								</div>
							</form>
						</div>
					</div>
					 
						
                </div>
                <!-- /.row -->
			</div>
		</div>
		<!-- /#adminpanel-->
		
	</div> 
	
	<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
	<div class="slides">
	</div>
	<h3 class="title"></h3>
	<a class="prev">
	‹ </a>
	<a class="next">
	› </a>
	<a class="close white">
	</a>
	<a class="play-pause">
	</a>
	<ol class="indicator">
	</ol>
</div>

	<!-- /#Contenedor-->
	<script src="admin/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
	<!-- The Templates plugin is included to render the upload/download listings -->
	<script src="admin/jquery-file-upload/js/vendor/tmpl.min.js"></script>
	<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
	<script src="admin/jquery-file-upload/js/vendor/load-image.min.js"></script>
	<!-- The Canvas to Blob plugin is included for image resizing functionality -->
	<script src="admin/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
	<script src="admin/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
	<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
	<script src="admin/jquery-file-upload/js/jquery.iframe-transport.js"></script>
	<!-- The basic File Upload plugin -->
	<script src="admin/jquery-file-upload/js/jquery.fileupload.js"></script>
	<!-- The File Upload processing plugin -->
	<script src="admin/jquery-file-upload/js/jquery.fileupload-process.js"></script>
	<!-- The File Upload image preview & resize plugin -->
	<script src="admin/jquery-file-upload/js/jquery.fileupload-image.js"></script>
	 
	<!-- The File Upload validation plugin -->
	<script src="admin/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
	<!-- The File Upload user interface plugin -->
	<script src="admin/jquery-file-upload/js/jquery.fileupload-ui.js"></script>
	<!--[if (gte IE 8)&(lt IE 10)]>
    <script src="admin/jquery-file-upload/js/cors/jquery.xdr-transport.js"></script>
    <![endif]-->
    <!--
    <script src="admin/js/form-fileupload.js"></script>
    -->
    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
<label class="title">
    <span>Titulo:</span><br>
    <input name="titulo[]" class="form-control">
</label>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger label label-danger"></strong>
        </td>
        <td>
            <p class="size">Procesando...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn blue start" disabled>
                    <i class="fa fa-upload"></i>
                    <span>Iniciar</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn red cancel">
                    <i class="fa fa-ban"></i>
                    <span>Cancelar</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td>
                    <span class="preview">
                        {% if (file.thumbnailUrl) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}"  data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                        {% } %}
                    </span>
                </td>
                <td>
                    <p class="name">
                        {% if (file.url) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                        {% } else { %}
                            <span>{%=file.name%}</span>
                        {% } %}
                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>
                    {% if (file.deleteUrl) { %}
                        <button class="btn red delete btn-sm" data-type="{%=file.deleteType%}" data-idimg="{%=file.id%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                            <i class="fa fa-trash-o"></i>
                            <span>Borrar</span>
                        </button>
                        <input type="checkbox" name="delete" value="1" class="toggle">
                    {% } else { %}
                        <button class="btn yellow cancel btn-sm">
                            <i class="fa fa-ban"></i>
                            <span>Cancelar</span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
    <script type="text/javascript">
   
    $(document).ready(function() {

    	$('a[data-toggle="tab"]').on('click', function(){
    		  if ($(this).parent('li').hasClass('disabled')) {
    		    return false;
    		  };
    		});

    	var itemTurismo = {
                'id' : '<?php if (isset($itemTurismoEdit)) { echo $itemTurismoEdit["Id"]; } else{ echo "null";}?>' 
            };
    	var itemTurismoJson = JSON.stringify(itemTurismo);
    	
    	$('#frmimagenes').fileupload({
    		  
    	    url: 'admin/funciones/post-turismo-img.php'
        	    
    	}).on('fileuploadsubmit', function (e, data) {
    		data.formData = {
    	            'itemTurismo': itemTurismoJson ,
    	            'data': data.context.find(':input').serializeArray()
    	        };

    	    
    	})  .on('fileuploaddestroy', function (e, data) {
  
    	}).on('fileuploaddone', function (e, data) {
        	         alert('paos papspo');
    		//traerImagenes();
    	    
    	}) ;

    	 $('#frmimagenes').addClass('fileupload-processing');
         
    	 traerImagenes();
        
        $("#frmturismo").submit(function(ev){
			//ev.preventDefault();
			 
			$inputNombre = $("#txtnombre");
			$inputPrecio = $("#txtprecio");
			$inputNombre.closest(".form-group").removeClass("has-error");
			$inputPrecio.closest(".form-group").removeClass("has-error");
			
			if ($inputNombre.val() === ""){
				$inputNombre.closest(".form-group").addClass("has-error");
				return false;
			}
			if ($inputPrecio.val() === ""){
				$inputPrecio.closest(".form-group").addClass("has-error");
				return false;
			}
			
			var $btnsend = $("#btnsendform").button('loading');
			 
		    //$btn.button('reset')
			
			return true;
        });
    });

    function traerImagenes()
    {
        $(".files").html("");
        
    	$.ajax({
            
            url: "admin/funciones/get-turismo-img.php",
            dataType: 'json',
            context: $('#frmimagenes')[0],
            data: {                   
                itemTurismo: '<?php if (isset($itemTurismoEdit)) { echo $itemTurismoEdit["Id"]; } else{ echo "null";}?>' 
            },
        
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
        });
    }
     
    </script>

</body>

</html>
