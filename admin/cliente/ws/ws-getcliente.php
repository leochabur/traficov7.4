<?php

class WSCliente extends nusoap_client{
	
	private $wsCli;
	private $wsErr;
	private $wsWSDL;
	private $wsUrl;
	
	private $err;
	
	public function __construct($url, $wsdl = true){
		 $this->wsUrl = $url;
		 $this->wsWSDL = $wsdl;
		 
	}
	
	public function getWsCli(){
		try{
		
			// Instancia Cliente
			$this->wsCli = new nusoap_client($this->wsUrl, $this->wsWSDL);		
			$this->wsErr  = $this->wsCli->getError();
				  
		}catch (Exception $exc){
			$this->wsErr = "Excepcion: " . $exc->getMessage();
		}
		
		return $this->wsCli;
	}
	 
	public function getWsReservas($metodo, $params){
	 	
		$result = null;
		try{
			$result	= $this->wsCli->call($metodo, $params );
			if($this->wsCli->fault)
			{
				$soapCliFallaCod = $oSoapSClient->faultcode;
				$soapCliFallaMsj = $oSoapSClient->faultstring;
			}
		}catch (Exception $ex){
			$this->wsErr = "Falló WsGetReservas.";
		}
		
		return $result;
		
	}
	 
}