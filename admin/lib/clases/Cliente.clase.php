<?php

require_once ('BdConexion.clase.php'); 
require_once ('Seguridad.clase.php');

class Cliente{
	
	private $clienteID,
			$user,  
			$pass,
			$nombre,
			$apellido, 
			$email,
			$telefono,
			$activo; 
	
	private $error;
	
	 
	public function __construct($nombre, $user, $email, $activo, $id = null){
		$this->clienteID = $id;		 
		$this->nombre = $nombre;
		$this->user = $user;
		$this->email = $email;
		$this->activo =$activo;
	}
	public function getError(){
		return $this->error;
	}
	
	public function getClienteId(){
		return $this->clienteID;
	}
	public function getUsername(){
		return $this->user;
	}
	public function getNombre(){
		return $this->nombre;
	}
	public function getApellido(){
		return $this->apellido;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getTelefono(){
		return $this->telefono;
	}
	public static function Login($user, $pass)
	{
			$resultado = array("ok"=>true, "msj"=>"Login exitoso!");
		
			
			if ((!isset($user) || ($user === "")) ||
					(!isset($pass) || ($pass === ""))) {
						$resultado ["msj"] = "Usuario y/o clave no son válidos.";
						$resultado ["ok"] =  false;
						return json_encode($resultado);
					}

			// PRUEBA
			return json_encode($resultado);
				
			$ClienteBd = null;
			try{
				// Campos ok
				$bd = new BdConexion();
				$query = "SELECT Id, Pass FROM cliente WHERE Username = :user AND FechaBaja IS NULL ; ";
				$bd->query($query);
				$bd->bind(':user', $user);
				$bd->execute();
				$ClienteBd = $bd->getFila();
				$bd = null;
					
			}catch (Exception $ex){
				$resultado ["msj"] = "Falló la conexión con la base de datos.";
				$resultado ["ok"] =  false;
				return json_encode($resultado);
			}
				
			if (empty($ClienteBd)){
				// Usuario no existe	
				$resultado ["msj"] = "Cliente no existe.";
				$resultado ["ok"] =  false;
				return json_encode($resultado);
			}else{
				if (!$ClienteBd['Activo']){
					// Usuario No Activo
					$resultado ["msj"] = "Su cuenta no está activa.";
					$resultado ["ok"] =  false;
					return json_encode($resultado);
				}
					
				// valida los datos (hashed password)
				if (!Seguridad::validaPasswordHash($pass,$ClienteBd['Pass'])){
						
					$resultado ["msj"] = "La clave ingresada no es válida.";
					$resultado ["ok"] =  false;
					return json_encode($resultado);
				}
			}
				
				
			return json_encode($resultado);
	
	}
	
	public static function cargarPorId($id){
			 
			$cliente = new self('Nombre','Username', 'clienteprueba@gmail.com', 1, $id);
			$cliente->apellido = 'Apellido';
			$cliente->telefono = 123456;
			return $cliente;		 
	}
	
	public static function getIdPorUsername($user){
	
		return 1244;			
	}
	 
	public static function getIdPorEmail($email){
	
		return 1244;
	}
	
	public function blanquearPass()
	{	
		$this->pass ="";
		$this->activo =false;		
	}
	
	public function getNuevaPass()
	{	
		$randSalt = Seguridad::randomSalt();
		$pass = Seguridad::hash_password($randSalt);
		$this->pass =$pass;
		return $this->pass;	
	}
}