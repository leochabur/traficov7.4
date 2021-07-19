<?php



use Doctrine\ORM\Mapping as ORM;

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
    private $estructura;

    /**
     * @var integer
     */
    private $capacidadTanque;

    /**
     * @var float
     */
    private $consumo;

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

    /**
     * Set capacidadTanque
     *
     * @param integer $capacidadTanque
     * @return Unidad
     */
    public function setCapacidadTanque($capacidadTanque)
    {
        $this->capacidadTanque = $capacidadTanque;

        return $this;
    }

    /**
     * Get capacidadTanque
     *
     * @return integer 
     */
    public function getCapacidadTanque()
    {
        return $this->capacidadTanque;
    }

    /**
     * Set consumo
     *
     * @param float $consumo
     * @return Unidad
     */
    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;

        return $this;
    }

    /**
     * Get consumo
     *
     * @return float 
     */
    public function getConsumo()
    {
        return $this->consumo;
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
     * Add acciones
     *
     * @param \AccionUnidad $acciones
     * @return Unidad
     */
    public function addAccione(\AccionUnidad $acciones)
    {
        $this->acciones[] = $acciones;

        return $this;
    }

    /**
     * Remove acciones
     *
     * @param \AccionUnidad $acciones
     */
    public function removeAccione(\AccionUnidad $acciones)
    {
        $this->acciones->removeElement($acciones);
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
}
