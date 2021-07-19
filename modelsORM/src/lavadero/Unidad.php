<?php



/**
 * Unidad
 */
class Unidad
{
    /**
     * @var integer
     */
    private $interno;

    /**
     * @var string
     */
    private $dominio;

    /**
     * @var string
     */
    private $nuevoDominio;

    /**
     * @var boolean
     */
    private $banio;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var integer
     */
    private $capacidad;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $acciones;

    /**
     * @var \Propietario
     */
    private $propietario;
    
    private $capacidadTanque;

	private $consumo;
    
    public function getCapacidadTanque(){
           return $this->capacidadTanque;
    }

    public function setCapacidadTanque($capacidad){
           $this->capacidadTanque = $capacidad;
    }

    
    
    public function getConsumo(){
           return $this->consumo;
    }

    public function setConsumo($consumo){
           $this->consumo = $consumo;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->acciones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set interno
     *
     * @param integer $interno
     *
     * @return Unidad
     */
    public function setInterno($interno)
    {
        $this->interno = $interno;
    
        return $this;
    }

    /**
     * Get interno
     *
     * @return integer
     */
    public function getInterno()
    {
        return $this->interno;
    }

    /**
     * Set dominio
     *
     * @param string $dominio
     *
     * @return Unidad
     */
    public function setDominio($dominio)
    {
        $this->dominio = $dominio;
    
        return $this;
    }

    /**
     * Get dominio
     *
     * @return string
     */
    public function getDominio()
    {
        return $this->dominio;
    }

    /**
     * Set nuevoDominio
     *
     * @param string $nuevoDominio
     *
     * @return Unidad
     */
    public function setNuevoDominio($nuevoDominio)
    {
        $this->nuevoDominio = $nuevoDominio;
    
        return $this;
    }

    /**
     * Get nuevoDominio
     *
     * @return string
     */
    public function getNuevoDominio()
    {
        return $this->nuevoDominio;
    }

    /**
     * Set banio
     *
     * @param boolean $banio
     *
     * @return Unidad
     */
    public function setBanio($banio)
    {
        $this->banio = $banio;
    
        return $this;
    }

    /**
     * Get banio
     *
     * @return boolean
     */
    public function getBanio()
    {
        return $this->banio;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Unidad
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set capacidad
     *
     * @param integer $capacidad
     *
     * @return Unidad
     */
    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;
    
        return $this;
    }

    /**
     * Get capacidad
     *
     * @return integer
     */
    public function getCapacidad()
    {
        return $this->capacidad;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add accione
     *
     * @param \AccionUnidad $accione
     *
     * @return Unidad
     */
    public function addAccione(\AccionUnidad $accione)
    {
        $this->acciones[] = $accione;
    
        return $this;
    }

    /**
     * Remove accione
     *
     * @param \AccionUnidad $accione
     */
    public function removeAccione(\AccionUnidad $accione)
    {
        $this->acciones->removeElement($accione);
    }

    /**
     * Get acciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAcciones()
    {
        return $this->acciones;
    }

    /**
     * Set propietario
     *
     * @param \Propietario $propietario
     *
     * @return Unidad
     */
    public function setPropietario(\Propietario $propietario = null)
    {
        $this->propietario = $propietario;
    
        return $this;
    }

    /**
     * Get propietario
     *
     * @return \Propietario
     */
    public function getPropietario()
    {
        return $this->propietario;
    }
    /**
     * @var integer
     */
    private $estructura;


    /**
     * Set estructura
     *
     * @param integer $estructura
     * @return Unidad
     */
    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;

        return $this;
    }

    /**
     * Get estructura
     *
     * @return integer 
     */
    public function getEstructura()
    {
        return $this->estructura;
    }

    public function __toString()
    {
        return $this->interno."";
    }

    /**
     * @var \TipoVehiculo
     */
    private $tipoUnidad;


    /**
     * Set tipoUnidad
     *
     * @param \TipoVehiculo $tipoUnidad
     * @return Unidad
     */
    public function setTipoUnidad(\TipoVehiculo $tipoUnidad = null)
    {
        $this->tipoUnidad = $tipoUnidad;

        return $this;
    }

    /**
     * Get tipoUnidad
     *
     * @return \TipoVehiculo 
     */
    public function getTipoUnidad()
    {
        return $this->tipoUnidad;
    }

    public function admiteAccion(TipoAccionUnidad $tipo)
    {
        if ($tipo->getBanio())
            return $this->banio;
        else
            return true;
    }    
    /**
     * @var string
     */
    private $marca;

    /**
     * @var string
     */
    private $modelo;

    /**
     * @var string
     */
    private $pase;

    /**
     * @var string
     */
    private $marca_motor;

    /**
     * @var boolean
     */
    private $video;

    /**
     * @var boolean
     */
    private $bar;

    /**
     * @var boolean
     */
    private $procesado;

    /**
     * @var integer
     */
    private $anio;

    /**
     * @var \MarcaParteVehiculo
     */
    private $marcaChasis;

    /**
     * @var \MarcaParteVehiculo
     */
    private $marcaMotor;

    /**
     * @var \CalidadCoche
     */
    private $calidad;

    /**
     * @var \TipoVehiculo
     */
    private $tipo;


    /**
     * Set marca
     *
     * @param string $marca
     * @return Unidad
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return string 
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set modelo
     *
     * @param string $modelo
     * @return Unidad
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;

        return $this;
    }

    /**
     * Get modelo
     *
     * @return string 
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * Set pase
     *
     * @param string $pase
     * @return Unidad
     */
    public function setPase($pase)
    {
        $this->pase = $pase;

        return $this;
    }

    /**
     * Get pase
     *
     * @return string 
     */
    public function getPase()
    {
        return $this->pase;
    }

    /**
     * Set marca_motor
     *
     * @param string $marcaMotor
     * @return Unidad
     */
    public function setMarcaMotor($marcaMotor)
    {
        $this->marca_motor = $marcaMotor;

        return $this;
    }

    /**
     * Get marca_motor
     *
     * @return string 
     */
    public function getMarcaMotor()
    {
        return $this->marca_motor;
    }

    /**
     * Set video
     *
     * @param boolean $video
     * @return Unidad
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return boolean 
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set bar
     *
     * @param boolean $bar
     * @return Unidad
     */
    public function setBar($bar)
    {
        $this->bar = $bar;

        return $this;
    }

    /**
     * Get bar
     *
     * @return boolean 
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * Set procesado
     *
     * @param boolean $procesado
     * @return Unidad
     */
    public function setProcesado($procesado)
    {
        $this->procesado = $procesado;

        return $this;
    }

    /**
     * Get procesado
     *
     * @return boolean 
     */
    public function getProcesado()
    {
        return $this->procesado;
    }

    /**
     * Set anio
     *
     * @param integer $anio
     * @return Unidad
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return integer 
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set marcaChasis
     *
     * @param \MarcaParteVehiculo $marcaChasis
     * @return Unidad
     */
    public function setMarcaChasis(\MarcaParteVehiculo $marcaChasis = null)
    {
        $this->marcaChasis = $marcaChasis;

        return $this;
    }

    /**
     * Get marcaChasis
     *
     * @return \MarcaParteVehiculo 
     */
    public function getMarcaChasis()
    {
        return $this->marcaChasis;
    }

    /**
     * Set calidad
     *
     * @param \CalidadCoche $calidad
     * @return Unidad
     */
    public function setCalidad(\CalidadCoche $calidad = null)
    {
        $this->calidad = $calidad;

        return $this;
    }

    /**
     * Get calidad
     *
     * @return \CalidadCoche 
     */
    public function getCalidad()
    {
        return $this->calidad;
    }

    /**
     * Set tipo
     *
     * @param \TipoVehiculo $tipo
     * @return Unidad
     */
    public function setTipo(\TipoVehiculo $tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \TipoVehiculo 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}
