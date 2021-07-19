<?php
use Doctrine\Common\Collections\ArrayCollection;
require_once ('Cliente.php');
require_once ('CanalPedido.php');
require_once ('Viaje.php');

/**
 * @author Leo
 * @version 1.0
 * @created 07-mar.-2018 9:05:16 p. m.
 */
class Presupuesto
{

	var $id;
	var $cliente;
	var $fechaSolicitud;
	var $montoSIva;
	var $iva;
	var $montoFinal;
	var $fechaConfeccion;
	var $pagoAnticipado;
	var $fechaInforme;/*** Representa la fecha en la que se le comunica el presupuesto al cliente*/	
	var $canalPedido;/*** A traves de que medio se solicito (mail, telefono, pagina web)*/
	var $emiteComprobante;
	var $viajes;
	var $observaciones;

    public function __construct() {
        $this->viajes = new ArrayCollection();
    }



	function getId()
	{
		return $this->id;
	}

	function getCliente()
	{
		return $this->cliente;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setCliente($newVal)
	{
		$this->cliente = $newVal;
	}

	function getFechaSolicitud()
	{
		return $this->fechaSolicitud;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setFechaSolicitud($newVal)
	{
		$this->fechaSolicitud = $newVal;
	}

	function getMontoSIva()
	{
		return $this->montoSIva;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setMontoSIva($newVal)
	{
		$this->montoSIva = $newVal;
	}

	function getIva()
	{
		return $this->iva;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setIva($newVal)
	{
		$this->iva = $newVal;
	}

	function getMontoFinal()
	{
		return $this->mntoFinal;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setMontoFinal($newVal)
	{
		$this->mntoFinal = $newVal;
	}

	function getFechaConfeccion()
	{
		return $this->fechaConfeccion;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setFechaConfeccion($newVal)
	{
		$this->fechaConfeccion = $newVal;
	}

	/**
	 * Representa la fecha en la que se le comunica el presupuesto al cliente
	 */
	function getFechaInforme()
	{
		return $this->fechaInforme;
	}

	/**
	 * Representa la fecha en la que se le comunica el presupuesto al cliente
	 * 
	 * @param newVal
	 */
	function setFechaInforme($newVal)
	{
		$this->fechaInforme = $newVal;
	}

	/**
	 * A traves de que medio se solicito (mail, telefono, pagina web)
	 */
	function getCanalPedido()
	{
		return $this->canalPedido;
	}

	/**
	 * A traves de que medio se solicito (mail, telefono, pagina web)
	 * 
	 * @param newVal
	 */
	function setCanalPedido($newVal)
	{
		$this->canalPedido = $newVal;
	}

}
?>