<?php
//Carga Doctrine
require_once(dirname(__FILE__) . '/lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
$conn = Doctrine_Manager::connection('mysql://root@localhost/ontuts_doctrine', 'doctrine');
$conn->setCharset('utf8'); 
Doctrine_Core::loadModels('models');

//Variable en la plantilla html
$tpl = array("comments"=> array(), "error"=>false);

//Comprueba si se ha enviado el formulario
if(!empty($_POST) && isset($_POST['create_comment'])){
	$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
	$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
	$text = filter_input(INPUT_POST, "text", FILTER_SANITIZE_STRING);
	//Comprueba que se hayan rellenado los campos obligatorios
	if(!empty($email) && !is_null($email) &&
	   !empty($text) && !is_null($text)){
		$userTable = Doctrine_Core::getTable('Users');
		$users = $userTable->findByEmail($email);
		$user = null;
		//Si el usuario no existe, lo crea
		if($users->count()==0){
			$user = new Users();
			$user->name = $name;
			$user->email = $email;
			$user->save();
		}else{
			$user = $users[0];
		}
		//Inserta el comentario
		$user->addComment($text);
	}else{
		//Si no se se han rellenado todos los valores obligatorios
		//mostrará un error
		$tpl['error'] = true;
	}
}
//Carga los comentarios
$commentsTable = Doctrine_Core::getTable('UsersComments');
$tpl['comments'] = $commentsTable->findAll();
//Envia la información
require_once('template.phtml');

