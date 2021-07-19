<?php
use Doctrine\Common\Collections\ArrayCollection;
require_once (dirname(dirname(__FILE__)).'/rrhh/Propietario.php');

/**
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:25 p. m.
 */
class Unidad
{

	protected $id;
	protected $interno;
	protected $banio;
	protected $dominio;
	protected $nuevoDominio;
	protected $propietario;
	protected $activo;
	protected $acciones; //representa las acciones que se realizaron sobre una unidad, Lavado interior, Exterior, Baño, Observaciones...etc
	protected $capacidad;
	protected $estructura;

    private $capacidadTanque;
    
    public function getCapacidadTanque(){
           return $this->capacidadTanque;
    }

    public function setCapacidadTanque($capacidad){
           $this->capacidadTanque = $capacidad;
    }

    private $consumo;
    
    public function getConsumo(){
           return $this->consumo;
    }

    public function setConsumo($consumo){
           $this->consumo = $consumo;
    }


	public function getEstructura(){
		return $this->estructura;
	}

	public function setEstructura($estructura){
		$this->estructura = $estructura;
	}

    public function __construct() {
        $this->acciones = new ArrayCollection();
    }



	public function getId()
	{
		return $this->id;
	}

	public function getInterno()
	{
		return $this->interno;
	}

	public function getBanio()
	{
		return $this->banio;
	}

	public function getCapacidad()
	{
		return $this->capacidad;
	}	

	/**
	 * 
	 * @param newVal
	 */
	public function setInterno($newVal)
	{
		$this->interno = $newVal;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setBanio($newVal)
	{
		$this->banio = $newVal;
	}

	public function getNuevoDominio()
	{
		return $this->nuevoDominio;
	}

	public function getAcciones()
	{
		return $this->acciones;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setNuevoDominio($newVal)
	{
		$this->nuevoDominio = $newVal;
	}

	public function getDominio()
	{
		return $this->dominio;
	}

	public function getActivo()
	{
		return $this->activo;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setDominio($newVal)
	{
		$this->dominio = $newVal;
	}

	public function getPropietario()
	{
		return $this->propietario;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setPropietario($newVal)
	{
		$this->propietario = $newVal;
	}

	public function setActivo($newVal)
	{
		$this->activo = $newVal;
	}

	public function setAcciones($newVal)
	{
		$this->acciones = $newVal;
	}

	public function setCapacidad($newVal)
	{
		$this->capacidad = $newVal;
	}	

	public function __toString()
	{
		return (string)$this->interno;
	}

	public function admiteAccion(TipoAccionUnidad $tipo)
	{
		if ($tipo->getBanio())
			return $this->banio;
		else
			return true;
	}

}
?>