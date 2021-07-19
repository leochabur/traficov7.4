<?php

require_once ('BdConexion.clase.php');
require_once ('Sesion.clase.php');

class ClienteSesion extends Sesion{
	
	private $IdCliente;	
	 
	public function __construct($nombre, $idcliente=null){
		
		parent::__construct($nombre);
		if (!$idcliente){
			$this->error = "Id de usuario inválido";
			 
		}		 
		$this->IdCliente = $idcliente;
		
	}
	  
	public function getIdCliente(){
		return $this->IdCliente;
	}
	   
	  
	public function getSesionBd(){
			
		if (!$this->estaActiva() || !$this->validaToken() || !$this->validaTokenUA()  ){
			return false;
		}
	 
		$bd = new BdConexion();
		// Guardo registro de Sesión del Cliente
		$query = "SELECT Id, IdCliente, Fecha, FechaLogout, Ip, Token, TokenUA FROM cliente_sesion WHERE Token = :token AND TokenUA = :tokenua; ";
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
			$this->IdCliente= $SesionBd['IdCliente'];
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
			// Actualizo Fecha de última sesion del Cliente
			$query = "INSERT INTO cliente_sesion (IdCliente, Fecha, Ip, Token, TokenUA) VALUES (:idcliente, :fecha, :ip, :token, :tokenua); ";
			$bd->query($query);
			$bd->bind(':idcliente', $this->IdCliente);
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
		// Actualizo Fecha de última sesion del Cliente
			$query = "UPDATE cliente_sesion SET FechaLogout = :fechalogout WHERE Id = :idsesion; ";
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