<?php


/**
 * @author Leo
 * @version 1.0
 * @created 07-mar.-2018 9:05:20 p. m.
 */
class CanalPedido
{

	var $id;
	var $canal;

	function CanalPedido()
	{
	}



	function getId()
	{
		return $this->id;
	}

	function getCanal()
	{
		return $this->canal;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setCanal($newVal)
	{
		$this->canal = $newVal;
	}

	public function  __toString()
	{
		return strtoupper($this->canal);
	}		

}
