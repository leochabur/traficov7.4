<?php



/**
 * TarifaTipoServicio
 */
class TarifaTipoServicio
{
    /**
     * @var float
     */
    private $importe = 0;

    /**
     * @var boolean
     */
    private $defecto;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \TipoVehiculo
     */
    private $tipo;

    /**
     * @var \TarifaServicio
     */
    private $tarifaServicio;


    /**
     * Set importe
     *
     * @param float $importe
     *
     * @return TarifaTipoServicio
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    
        return $this;
    }

    /**
     * Get importe
     *
     * @return float
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set default
     *
     * @param boolean $default
     *
     * @return TarifaTipoServicio
     */
    public function setDefecto($default)
    {
        $this->defecto = $default;
    
        return $this;
    }

    /**
     * Get default
     *
     * @return boolean
     */
    public function getDefecto()
    {
        return $this->defecto;
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
     * Set tipo
     *
     * @param \TipoVehiculo $tipo
     *
     * @return TarifaTipoServicio
     */
    public function setTipo(\TipoVehiculo $tipo = null)
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

    /**
     * Set tarifaServicio
     *
     * @param \TarifaServicio $tarifaServicio
     *
     * @return TarifaTipoServicio
     */
    public function setTarifaServicio(\TarifaServicio $tarifaServicio = null)
    {
        $this->tarifaServicio = $tarifaServicio;
    
        return $this;
    }

    /**
     * Get tarifaServicio
     *
     * @return \TarifaServicio
     */
    public function getTarifaServicio()
    {
        return $this->tarifaServicio;
    }

    public function __toString(){
        if ($this->articulo)
            return $this->articulo->getDescripcion();
        else
            return "NO EXISTE ARTICULO ----------------".$this->id;
    }    
}
