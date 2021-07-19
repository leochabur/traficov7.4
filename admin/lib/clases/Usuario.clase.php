<?php

require_once ('BdConexion.clase.php'); 
require_once ('Seguridad.clase.php');

class Usuario{
	
	private $userId,
			$user, 
			$pass;
	
	private $nombre,
			$apellido, 
			$email,
			$activo,
			$observ,
	  		$fechaAlta,
			$fechaEdit, 
			$fechaBaja, 
			$fechaUltSesion;	
	
	private $error;
	
	public function __construct($nombre, $user, $email, $activo, $id = null){
		$this->userId = $id;
		$this->nombre = $nombre;
		$this->user = $user;
		$this->email = $email;
		$this->activo =$activo;
	}
	 
	public function getError(){
		return $this->error;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	public function getUser(){
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
	public function getActivo(){
		if (!$this->activo){
			// Usuario No Activo
			$this->error = "Su cuenta no está activa.";
			return false;
		}
		return $this->activo;
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
				 
			$UserBd = null;
			try{
				// Campos ok
				$bd = new BdConexion();
				$query = "SELECT Id, Activo, Pass FROM usuario WHERE Username = :user AND FechaBaja IS NULL ; ";
				$bd->query($query);
				$bd->bind(':user', $user);
				$bd->execute();
				$UserBd = $bd->getFila();
				$bd = null;
					
			}catch (Exception $ex){
				$resultado ["msj"] = "Falló la conexión con la base de datos.";
				$resultado ["ok"] =  false;
				return json_encode($resultado);
			}
				
			if (empty($UserBd)){
				// Usuario no existe
				$resultado ["msj"] = "El Usuario no existe.";
				$resultado ["ok"] =  false;
				return json_encode($resultado);
			}else{
				if (!$UserBd['Activo']){
					// Usuario No Activo
					$resultado ["msj"] = "Su cuenta no está activa.";
					$resultado ["ok"] =  false;
					return json_encode($resultado);
				}
					
				// valida los datos (hashed password)
				if (!Seguridad::validaPasswordHash($pass,$UserBd['Pass'])){
						
					$resultado ["msj"] = "La clave ingresada no es válida.";
					$resultado ["ok"] =  false;
					return json_encode($resultado);
				}
			}
				
				
			return json_encode($resultado);

	}
	
	public static function updateUltSesion($id)
	{
		$bd = new BdConexion();
		// Actualizo Fecha de última sesion del usuario
		$query = "UPDATE usuario SET FechaUltSesion = NOW() WHERE Id = :id ; ";
		$bd->query($query);
		$bd->bind(':id', $id); 
		$bd->execute();
		    
		$bd =null;
	}
	
	public static function getIdPorUsername($user){
	
		 
		$bd = new BdConexion();
		$bd->query("SELECT Id  FROM usuario WHERE Username = :user AND FechaBaja IS NULL ; "); 
		$bd->bind(':user', $user);
		$bd->execute();
		$registro = $bd->getFila();
		if($registro){
			return $registro['Id'];				
		}else{
			return null;
		}
		 
	}
	
	public static function cargarPorId($id)
	{
		 
		$bd = new BdConexion();
		$bd->query('SELECT Id,Nombre, Apellido, Email, Username, Activo, Observ, FechaAlta, FechaEdit, FechaUltSesion FROM usuario WHERE Id = :id');
		$bd->bind(':id', $id);
		$bd->execute();
		$registro = $bd->getFila();
		if($registro){
			$user = new self($registro['Nombre'],$registro['Username'], $registro['Email'], $registro['Activo'],$registro['Id']);
			$user->apellido = $registro['Apellido'];
			$user->observ = $registro['Observ'];
			$user->fechaUltSesion= $registro['FechaUltSesion'];		
			return $user;
			
		}else{			
			return null;
		}
		 
	}
	
}