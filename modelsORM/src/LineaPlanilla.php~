<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * LineaPlanilla
 */
class LineaPlanilla
{
    /**
     * @var string
     */
    private $nombreLinea;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Ciudad
     */
    private $localidad;

    /**
     * @var \ArticuloCliente
     */
    private $articulo;

    /**
     * @var \Servicio
     */
    private $entrada;

    /**
     * @var \Servicio
     */
    private $salida;

    /**
     * @var \BloquePlanilla
     */
    private $bloque;

    public function __toString()
    {
        return $this->nombreLinea;
    }

    /**
     * Set nombreLinea
     *
     * @param string $nombreLinea
     * @return LineaPlanilla
     */
    public function setNombreLinea($nombreLinea)
    {
        $this->nombreLinea = $nombreLinea;

        return $this;
    }

    /**
     * Get nombreLinea
     *
     * @return string 
     */
    public function getNombreLinea()
    {
        return $this->nombreLinea;
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
     * Set localidad
     *
     * @param \Ciudad $localidad
     * @return LineaPlanilla
     */
    public function setLocalidad(\Ciudad $localidad)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return \Ciudad 
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set articulo
     *
     * @param \ArticuloCliente $articulo
     * @return LineaPlanilla
     */
    public function setArticulo(\ArticuloCliente $articulo = null)
    {
        $this->articulo = $articulo;

        return $this;
    }

    /**
     * Get articulo
     *
     * @return \ArticuloCliente 
     */
    public function getArticulo()
    {
        return $this->articulo;
    }

    /**
     * Set entrada
     *
     * @param \Servicio $entrada
     * @return LineaPlanilla
     */
    public function setEntrada(\Servicio $entrada = null)
    {
        $this->entrada = $entrada;

        return $this;
    }

    /**
     * Get entrada
     *
     * @return \Servicio 
     */
    public function getEntrada()
    {
        return $this->entrada;
    }

    /**
     * Set salida
     *
     * @param \Servicio $salida
     * @return LineaPlanilla
     */
    public function setSalida(\Servicio $salida = null)
    {
        $this->salida = $salida;

        return $this;
    }

    /**
     * Get salida
     *
     * @return \Servicio 
     */
    public function getSalida()
    {
        return $this->salida;
    }

    /**
     * Set bloque
     *
     * @param \BloquePlanilla $bloque
     * @return LineaPlanilla
     */
    public function setBloque(\BloquePlanilla $bloque = null)
    {
        $this->bloque = $bloque;

        return $this;
    }

    /**
     * Get bloque
     *
     * @return \BloquePlanilla 
     */
    public function getBloque()
    {
        return $this->bloque;
    }
    /**
     * @var float
     */    
    private $orden;


    /**
     * Set orden
     *
     * @param float $orden
     * @return LineaPlanilla
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return float 
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
