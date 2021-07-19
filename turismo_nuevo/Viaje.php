<?php
require_once ('Ciudad.php');
require_once ('..\modelsORM\src\turismo\ServicioViaje.php');

/**
 * @author Leo
 * @version 1.0
 * @created 15-mar.-2018 4:47:19 p. m.
 */
class Viaje
{

	var $id;
	var $origen;
	var $destino;
	var $lugarSalida;
	var $lugarLlegada;
	var $hSalida;
	var $hLlegada;
	var $pax;
	var $km;
	var $observaciones;
	var $fSalida;
	var $fLlegada;
	var $m_Ciudad;
	var $m_ServicioViaje;

	function Viaje()
	{
	}



	function getId()
	{
		return $this->id;
	}

	function getOrigen()
	{
		return $this->origen;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setOrigen($newVal)
	{
		$this->origen = $newVal;
	}

	function getDestino()
	{
		return $this->destino;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setDestino($newVal)
	{
		$this->destino = $newVal;
	}

	function getLugarSalida()
	{
		return $this->lugarSalida;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setLugarSalida($newVal)
	{
		$this->lugarSalida = $newVal;
	}

	function getLugarLlegada()
	{
		return $this->lugarLlegada;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setLugarLlegada($newVal)
	{
		$this->lugarLlegada = $newVal;
	}

	function getHSalida()
	{
		return $this->hSalida;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setHSalida($newVal)
	{
		$this->hSalida = $newVal;
	}

	function getHLlegada()
	{
		return $this->hLlegada;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setHLlegada($newVal)
	{
		$this->hLlegada = $newVal;
	}

	function getPax()
	{
		return $this->pax;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setPax($newVal)
	{
		$this->pax = $newVal;
	}

	function getKm()
	{
		return $this->km;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setKm($newVal)
	{
		$this->km = $newVal;
	}

	function getObservaciones()
	{
		return $this->observaciones;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setObservaciones($newVal)
	{
		$this->observaciones = $newVal;
	}

	function getFLlegada()
	{
		return $this->fLlegada;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setFLlegada($newVal)
	{
		$this->fLlegada = $newVal;
	}

	function getFSalida()
	{
		return $this->fSalida;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setFSalida($newVal)
	{
		$this->fSalida = $newVal;
	}

}
?>