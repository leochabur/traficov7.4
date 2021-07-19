<?php


/**
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:32 p. m.
 */
class Propietario
{

	protected $id;
	protected $razonSocial;
	protected $activo;
	protected $estructura;	
	
	public function _construct()
	{
	}



	function getId()
	{
		return $this->id;
	}

	function getRazonSocial()
	{
		return $this->razonSocial;
	}

	function getActivo()
	{
		return $this->activo;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setRazonSocial($newVal)
	{
		$this->razonSocial = $newVal;
	}

	function setActivo($newVal)
	{
		$this->activo = $newVal;
	}

}
