<?php

require_once ('BdConexion.clase.php');

class UsuarioSesion{
	
	private $Id;
	private $IdUsuario;	
	private $error; 
	private $timeout;
	private $token;
	private $activa;
	private $tokenUA;
	private $horaIni, $horaFin;
	private $ip;
	
	public function __construct($iduser=null){
		
		$this->timeout 	= (10*60);
		$this->IdUsuario = $iduser;
		$this->activa = false;
		$this->error ="";
	}
	 
	// Genera el token para el UserAgent
	public function setTokenUA(){
	
		$string = $_SERVER['HTTP_USER_AGENT'];
		$string .= 'DAGUERROTIPO';
		$fingerprint = md5($string);
		$this->tokenUA = $fingerprint;
		
	}
	 
	public function setToken(){
		$this->token = md5(rand(0,9999999));
	}

	public function getIdUsuario(){
		return $this->IdUsuario;
	}
	
	public function getToken(){
		return $this->token;
	}
	
	public function getTokenUA(){
		return $this->tokenUA;
	}
	
	public function getError(){
		return $this->error;
	}
	
	// Chequea sesion "activa"
	public function estaActiva(){	
		if ((!isset($_SESSION["activa"])) || (!$_SESSION["activa"]))
		{
			$this->error .= "La sesión no está activa.";
			return false;
		}
		return true;
	}
	
	public function validaToken(){

		if ( !isset($_SESSION['token']) ){
			$this->error .= "El token de la sesión no es válido.";
			return false;
		}
		return true;
	}
	
	public function validaTokenUA(){
		
 		if ( !isset($_SESSION['UA']) ){
			$this->error .= "Ingresó con otro navegador.";
			return false;
		}
		return true;
	}
	
	public function getSesionBd(){
		 
		if (!$this->estaActiva() || !$this->validaToken() || !$this->validaTokenUA()  ){
			return false;
		}
		
		$token 		= $_SESSION['token'];
		$tokenUA 	= $_SESSION['UA'];
		
		$bd = new BdConexion();
		// Guardo registro de Sesión del usuario 
		$query = "SELECT Id, IdUsuario, Fecha, FechaLogout, Ip, Token, TokenUA FROM usuario_sesion WHERE Token = :token AND TokenUA = :tokenua; ";
		$bd->query($query);
		//$bd->bind(':fecha', date("Y-m-d H:i:s", $this->horaIni));
		$bd->bind(':token', $token);
		$bd->bind(':tokenua', $tokenUA);
		$bd->execute(); 
		
		$SesionBd = $bd->getFila();
		if (!empty($SesionBd)){
			
			if($SesionBd['Token'] !== $_SESSION['token']){
				$this->error .= "Token inválido.";
				return false;
			}
			if($SesionBd['TokenUA'] !== $_SESSION['UA']){
				$this->error .= "Token UA inválido.";
				return false;
			}
			$this->Id 		= $SesionBd['Id'];
			$this->IdUsuario= $SesionBd['IdUsuario'];
			$this->activa 	= true;
			$this->token 	= $SesionBd['Token'];
			$this->tokenUA 		= $SesionBd['TokenUA'];
			$this->horaIni 	= $SesionBd['Fecha'];
			$this->horaFin 	= $SesionBd['FechaLogout'];
				
		}
		
		$bd = null;
		
		return true;
	}
	 
	// Inicia variables sesion
	public function sesionIni(){
		
		session_regenerate_id();
		
		$this->horaIni 	= time();
		$this->horaFin 	= null;
		$this->activa 	= true;
		$this->setToken();
		$this->setTokenUA();  
		
		$_SESSION['activa'] 	= $this->activa;
		$_SESSION['token'] 		= $this->token;
		$_SESSION['UA'] 		= $this->tokenUA;
		$_SESSION['horaIn'] 	= $this->horaIni;
		$_SESSION['horaOut'] 	= $this->horaFin;
		  
	}
	
	public function guardarSesion(){
		$fechahora = date("Y-m-d H:i:s", $this->horaIni);
		
		$bd = new BdConexion();
		// Actualizo Fecha de última sesion del usuario
		$query = "INSERT INTO usuario_sesion (IdUsuario, Fecha, Ip, Token, TokenUA) VALUES (:idusuario, :fecha, :ip, :token, :tokenua); ";
		$bd->query($query);
		$bd->bind(':idusuario', $this->IdUsuario);
		$bd->bind(':fecha', $fechahora);
		$bd->bind(':ip', $_SERVER['SERVER_ADDR']);
		$bd->bind(':token', $this->token);
		$bd->bind(':tokenua', $this->tokenUA);
		$bd->execute();
		$this->Id = $bd->lastInsertId();
		$bd = null;
	}
	
	// Cierra sesion
	public function sesionFin(){
	
		$this->horaFin = time();		
		$this->activa = false;
		$this->token = "";
		$this->tokenUA = "";
	 
		session_unset();
		session_destroy();
		
		$this->updateLogout();
	}
	
	public function updateLogout(){
		
		$fechahora = date("Y-m-d H:i:s", $this->horaFin);
		
		$bd = new BdConexion();
		// Actualizo Fecha de última sesion del usuario
		$query = "UPDATE usuario_sesion SET FechaLogout = :fechalogout WHERE Id = :idsesion; ";
		$bd->query($query);
		$bd->bind(':fechalogout', $fechahora);
		$bd->bind(':idsesion', $this->Id);
		$bd->execute();
		$bd = null;
		
	}
	   
}