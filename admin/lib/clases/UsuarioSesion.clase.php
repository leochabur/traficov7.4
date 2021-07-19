<?php

require_once ('BdConexion.clase.php');
require_once ('Sesion.clase.php');

class UsuarioSesion extends Sesion{
	 
	private $IdUsuario;	 
	
	public function __construct($nombre,$iduser=null){
		
		parent::__construct($nombre);
		if (!$iduser){
			$this->error = "Id de usuario inválido";
			 
		}		 
		$this->IdUsuario = $iduser;
		 
	}
	public function getError(){
		return $this->error;
	}
	public function getIdUsuario(){
		return $this->IdUsuario;
	}
	
	public  function getSesionBd(){
		 
		if (!$this->estaActiva() || !$this->validaToken() || !$this->validaTokenUA()  ){
			return false;
		}
		 
		$bd = new BdConexion();
		// Guardo registro de Sesión del usuario 
		$query = "SELECT Id, IdUsuario, Fecha, FechaLogout, Ip, Token, TokenUA FROM usuario_sesion WHERE Token = :token AND TokenUA = :tokenua; ";
		$bd->query($query); 
		$bd->bind(':token', $this->sesion['token']);
		$bd->bind(':tokenua', $this->sesion['tokenUA']);
		$bd->execute(); 
		
		$SesionBd = $bd->getFila();

		$bd = null;
		
		if (!empty($SesionBd)){
			
			if($SesionBd['Token'] !== $this->sesion['token']){
				$this->error .= "Token inválido.";
				return false;
			}
			
			if($SesionBd['TokenUA'] !== $this->sesion['tokenUA']){
				$this->error .= "Token UA inválido.";
				return false;
			}
			
			$this->Id 		= $SesionBd['Id'];
			$this->IdUsuario= $SesionBd['IdUsuario'];
			$this->activa 	= true;
			$this->token 	= $SesionBd['Token'];
			$this->tokenUA 	= $SesionBd['TokenUA'];
			$this->horaIni 	= $SesionBd['Fecha'];
			$this->horaFin 	= $SesionBd['FechaLogout'];
				
		}
		
		
		return true;
	}
	  
	public  function guardarSesionBD(){
		
		$fechahora = date("Y-m-d H:i:s", $this->horaIni);
		
		$bd = new BdConexion();
		try{
			// Actualizo Fecha de última sesion del usuario
			$query = "INSERT INTO usuario_sesion (IdUsuario, Fecha, Ip, Token, TokenUA) VALUES (:idusuario, :fecha, :ip, :token, :tokenua); ";
			$bd->query($query);
			$bd->bind(':idusuario', $this->IdUsuario);
			$bd->bind(':fecha', $fechahora);
			$bd->bind(':ip', $this->ip);
			$bd->bind(':token', $this->token);
			$bd->bind(':tokenua', $this->tokenUA);
			$bd->execute();
			$this->Id = $bd->lastInsertId();
		}catch (PDOException $ex){
			$this->error = "No se pudo guardar la sesión.";
			}
		catch (Exception $ex){
			$this->error = "No se pudo guardar la sesión.";
		}
		$bd = null;
	}
	 
	public  function updateLogoutBD(){
		
		$fechahora = date("Y-m-d H:i:s", $this->horaFin);
		
		$bd = new BdConexion();
		try{
			// Actualizo Fecha de última sesion del usuario
			$query = "UPDATE usuario_sesion SET FechaLogout = :fechalogout WHERE Id = :idsesion; ";
			$bd->query($query);
			$bd->bind(':fechalogout', $fechahora);
			$bd->bind(':idsesion', $this->Id);
			$bd->execute();
		}catch (PDOException $ex){
			$this->error = "No se pudo actualizar el cierre de la sesión.";
		}
		catch (Exception $ex){
			$this->error = "No se pudo actualizar el cierre de la sesión.";
		}
		$bd = null;
		
	}
	   
}
