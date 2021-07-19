<?php

class Sesion{
	
	protected $Id;
	protected $SessId;
	protected $SessPrefix;
	protected $error;
	protected $timeout;
	protected $token;
	protected $tokenUA;
	protected $activa;	
	protected $horaIni, $horaFin;
	protected $ip;
	protected $UA;
	protected $sesion;
	
	protected function __construct($prefix){
	
		$this->timeout 		= (10*60); 
		$this->activa 		= false;
		$this->error 		= "";
		$this->SessPrefix 	= $prefix; 
		$this->sesion 		= isset($_SESSION[$this->SessPrefix]) ? $_SESSION[$this->SessPrefix] : null;
		$this->UA 			= $_SERVER['HTTP_USER_AGENT'];
		$this->ip 			= $_SERVER['REMOTE_ADDR'];
		
	}
	
	// Genera el token para el UserAgent
	protected function setTokenUA(){	 
		$string = 'DAGUERROTIPO'; 
		$this->tokenUA = md5($this->UA . $string);	
	}
	
	protected function setToken(){
		$this->token = md5(rand(0,9999999));
	}
	 
	protected function getToken(){
		return $this->token;
	}
	
	protected function getTokenUA(){
		return $this->tokenUA;
	}
	
	protected function getError(){
		return $this->error;
	}
	
	// Chequea sesion "activa"
	protected function estaActiva(){
		if ((!isset($this->sesion["activa"])) || (!$this->sesion["activa"]))
		{
			$this->error .= "La sesión no está activa. ";
			return false;
		}
		return true;
	}
	
	protected function validaToken(){
	
		if ( !isset($this->sesion['token']) ){
			$this->error .= "El token de la sesión no es válido. ";
			return false;
		}
		return true;
	}
	
	protected function validaTokenUA(){
	
		if ( !isset($this->sesion['tokenUA']) ){
			$this->error .= "Ingresó con otro navegador. ";
			return false;
		}
		return true;
	}
	
	// Inicia variables sesion
	public function sesionIni(){
	
		//session_id($this->guid);
		session_regenerate_id();
	
		$this->horaIni 	= time();
		$this->horaFin 	= null;
		$this->activa 	= true;
		$this->setToken();
		$this->setTokenUA();
		 
		$_SESSION[$this->SessPrefix]['activa'] = $this->activa;
		$_SESSION[$this->SessPrefix]['token'] 	= $this->token;
		$_SESSION[$this->SessPrefix]['tokenUA']= $this->tokenUA;
		$_SESSION[$this->SessPrefix]['horaIn'] = $this->horaIni;
		$_SESSION[$this->SessPrefix]['horaOut']= $this->horaFin;
		$this->sesion = $_SESSION[$this->SessPrefix];
	}
	
	
	// Cierra sesion
	public function sesionFin(){

		$this->horaFin = time();
		$this->updateLogoutBD();
		
		$this->Id = null;		
		$this->activa = false;
		$this->token = null;
		$this->tokenUA = null;
		$this->ip = null;
		$this->UA = null;
		$_SESSION[$this->SessPrefix] = null;
		$this->sesion = null;
		//session_unset();
		//session_destroy();
	
	}
	 
	// Trae registro de sesion de BD
	protected function getSesionBd(){
	
	}
	
	
	// Almacena en BD
	protected function guardarSesionBD(){
	
	}
	
	// Actualiza logout de sesion en BD
	protected function updateLogoutBD(){
	 
	}
	
}