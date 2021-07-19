<?php


/**
 * @author Leo
 * @version 1.0
 * @created 12-mar.-2018 8:03:50 p. m.
 */
class ServicioViaje
{

	var $id;
	var $servicio;

	function ServicioViaje()
	{
	}



	function getId()
	{
		return $this->id;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setId($newVal)
	{
		$this->id = $newVal;
	}

	function getServicio()
	{
		return $this->servicio;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setServicio($newVal)
	{
		$this->servicio = $newVal;
	}

	public function __toString()
	{
		return strtoupper($this->servicio);
	}		

}
?>