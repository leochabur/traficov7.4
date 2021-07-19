<?php
require_once ('Ciudad.php');
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Leo
 * @version 1.0
 * @created 07-mar.-2018 9:05:19 p. m.
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
	var $servicios;
	var $hSalidaRegreso;
	var $hLlegadaRegreso;
	var $fSalida;
	var $fRegreso;
	var $presupuesto;
	private $eliminado;

	public function _construct()
	{
		$this->servicios = new ArrayCollection();
	}

	public function setEliminado($newVal)
	{
		$this->eliminado = $newVal;
	}

	public function getEliminado()
	{
		return $this->eliminado;
	} 	


	function getFSalida()
	{
		return $this->fSalida;
	}

	function getFRegreso()
	{
		return $this->fRegreso;
	}

	function setFSalida($newVal)
	{
		$this->fSalida = $newVal;
	}

	function setFRegreso($newVal)
	{
		$this->fRegreso = $newVal;
	}

	function getHSalidaRegreso()
	{
		return $this->hSalidaRegreso;
	}

	function getHLlegadaRegreso()
	{
		return $this->hLlegadaRegreso;
	}

	function setHSalidaRegreso($newVal)
	{
		$this->hSalidaRegreso = $newVal;
	}

	function setHLlegadaRegreso($newVal)
	{
		$this->hLlegadaRegreso = $newVal;
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

	public function addServicio($servicio)
	{
		$this->servicios[] = $servicio;
	}

	public function setPresupuesto(Presupuesto $pres)
    {
        $this->presupuesto = $pres; 
    }

	public function getPresupuesto()
    {
       return $this->presupuesto; 
    }	    	

    public function setPreUpdateAction()
    {
    	$this->eliminado = false;
    }

}
