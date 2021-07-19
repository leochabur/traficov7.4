<?php
use Doctrine\Common\Collections\ArrayCollection;
require_once ('Unidad.php');
require_once ('TipoAccionUnidad.php');
require_once (dirname(dirname(__FILE__)).'/rrhh/Empleado.php');


/**
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:28 p. m.
 */
class AccionUnidad
{

	protected $id;
	protected $fecha;
	protected $unidad;
	protected $accion;
	protected $responsables;
	protected $fechaAlta;
	protected $observaciones;

	public function _construct()
	{
		$this->responsables = new ArrayCollection();
	}



	public function getFecha()
	{
		return $this->fecha;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setFecha($newVal)
	{
		$this->fecha = $newVal;
	}

	public function getUnidad()
	{
		return $this->unidad;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setUnidad(Unidad $newVal)
	{
		$this->unidad = $newVal;
	}

	public function getAccion()
	{
		return $this->accion;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getResponsables()
	{
		return $this->responsables;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setAccion(TipoAccionUnidad $newVal)
	{
		$this->accion = $newVal;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setResponsables($newVal)
	{
		$this->responsables = $newVal;
	}

	public function getFechaAlta()
	{
		return $this->fechaAlta;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setFechaAlta($newVal)
	{
		$this->fechaAlta = $newVal;
	}

	public function getObservaciones()
	{
		return $this->observaciones;
	}

	public function setObservaciones($newVal)
	{
		$this->observaciones = $newVal;
	}

	public function addResponsable($responsable)
	{
		$this->responsables[] = $responsable;
	}

	public function actualizarHorario()
	{
		date_default_timezone_set('America/Argentina/Buenos_Aires');
		//$this->fecha = new \DateTime();
		$this->fechaAlta = new \DateTime();
	}

	public function responsablesList()
	{
		$res = "";
		foreach ($this->responsables as $value) {
			$res.=$value->getApellido()." - ";
		}
		return $res;
	}	
    
}
?>