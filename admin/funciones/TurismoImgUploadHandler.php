<?php
require ('general.php');
require ('UploadHandler.php');

class TurismoImgUploadHandler extends UploadHandler
{
	 public $uploadPathSinRaiz, $uploadPathConRaiz;
	 
	protected function initialize() {
		 
		parent::initialize();
		 
	}
	
	protected function get_upload_path($file_name = null, $version = null) {
		
		$this->uploadPathSinRaiz = parent::get_upload_path($file_name, $version);
		
		$this->uploadPathConRaiz = $this->options["raiz"].$this->uploadPathSinRaiz;		
		
		return $this->uploadPathConRaiz;
	}
	
	function getDeleteUrl($file){
		$deleteUrl = $this->options['url_script_del']
		.'?idImg='.$file->id
		."&itemTurismo=".$this->options["IdTurismo"]
		."&".$this->get_singular_param_name()
		.'='.rawurlencode($file->name);
		if ($file->deleteType !== 'DELETE') {
			$deleteUrl .= '&_method=DELETE';
		}	
		return $deleteUrl;
	}
	
	protected function handle_form_data($file, $index) {
		$file->titulo = @$_REQUEST['titulo'][$index]; 
		
	}
	
	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
			$index = null, $content_range = null) 
	{
		$info = pathinfo($name);
		$name = sanear_string($info["filename"]) . "." .$info["extension"];
		$file = parent::handle_file_upload(
				$uploaded_file, $name, $size, $type, $error, $index, $content_range
		);
		if (empty($file->error)) {
			
			$bd = new BdConexion();
		 
			$sqlAlta = "INSERT INTO imagenes_por_sociales (id_social, imagen)
				VALUES (:idturismo, :imgorig); ";
			$bd->query($sqlAlta);
			$bd->bind(':idturismo', $this->options["IdTurismo"]);
			
			$this->get_upload_path($file->name);
			$bd->bind(':imgorig', $this->uploadPathSinRaiz);
				
		/*	$this->get_upload_path($file->name, 'thumbnail');
			$bd->bind(':imgchica', $this->uploadPathSinRaiz);
			
			$this->get_upload_path($file->name, 'medium');
			$bd->bind(':imgmedia', $this->uploadPathSinRaiz);
			
			$this->get_upload_path($file->name, 'grande');
			$bd->bind(':imggrande', $this->uploadPathSinRaiz);   */
			$bd->execute();

			$file->id = $bd->lastInsertId();
			
			$file->deleteUrl = $this->getDeleteUrl($file);
			
			$bd = null;
		 
		}
		return $file;
	}
	 
	protected function set_additional_file_properties($file) {
		parent::set_additional_file_properties($file);
		
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$sqlGet = "SELECT id, id_social, imagen
					FROM imagenes_por_sociales
				WHERE (id_social =:idturismo) AND (imagen = :img); ";
			$bd = new BdConexion();
			$bd->query($sqlGet);
			$bd->bind(':img', $this->options["upload_dir"].$file->name);
			$bd->bind(':idturismo', $this->options["IdTurismo"]);			 
			$bd->execute();
			  $filas = $bd->getFilas();
			  $bd = null;
			  
			foreach($filas as $fila) {
				$file->id = $fila["id"];
				$file->title = $fila["imagen"];
				$file->deleteUrl = $this->getDeleteUrl($file);
			}
		}
	}
	
	public function delete($print_response = true) {
		$response = parent::delete(false);
		 
		foreach ($response as $name => $deleted) {
			if ($deleted) {
				//$sql = "DELETE FROM turismo_imagen WHERE Img_Orig = :img_orig AND IdTurismo = :idturismo";
				$sql = "UPDATE turismo_imagen SET FechaBaja = NOW() WHERE Id = :id AND IdTurismo = :idturismo";
				$bd = new BdConexion();
				$bd->query($sql);
				$bd->bind(':id', $this->options["IdImg"]); 
				$bd->bind(':idturismo', $this->options["IdTurismo"]);
				$bd->execute();
				
				$bd = null;
			}
		} 
		return $this->generate_response($response, $print_response);
	} 
	 
}

