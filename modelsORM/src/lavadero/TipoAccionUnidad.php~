<?php


/**
 * Representa el tipo de accion que se puede realizar sobre un interno.
 * X ej. Lavado Interior, Lavado Exterior....etc
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:27 p. m.
 */
class TipoAccionUnidad
{

	var $id;
	var $tipo;
	var $prioritaria;
	protected $banio;	
	protected $comenta;	

	function TipoAccionUnidad()
	{
	}



	function getTipo()
	{
		return $this->tipo;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setTipo($newVal)
	{
		$this->tipo = $newVal;
	}

	function getId()
	{
		return $this->id;
	}

	function getPrioritaria()
	{
		return $this->prioritaria;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setPrioritaria($newVal)
	{
		$this->prioritaria = $newVal;
	}

	public function __toString()
	{
		return $this->tipo;
	}

	public function getBanio()
	{
		return $this->banio;
	}

	public function setBanio($newVal)
	{
		$this->banio = $newVal;
	}

	public function getComenta()
	{
		return $this->comenta;
	}

	public function setComenta($newVal)
	{
		$this->comenta = $newVal;
	}	
}
