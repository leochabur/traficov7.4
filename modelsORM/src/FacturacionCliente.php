<?php
use \Doctrine\Common\Collections\ArrayCollection;
/**
 * FacturacionCliente
 */
class FacturacionCliente
{
    /**
     * @var string
     */
    private $tipoFacturacion;

    /**
     * @var boolean
     */
    private $calculaHExtra;

    /**
     * @var float
     */
    private $importeHExtra;

    /**
     * @var integer
     */
    private $id;

    /**
     */
    private  $tarifas;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifas = new ArrayCollection();
    }

    /**
     * Set tipoFacturacion
     *
     * @param string $tipoFacturacion
     *
     * @return FacturacionCliente
     */
    public function setTipoFacturacion($tipoFacturacion)
    {
        $this->tipoFacturacion = $tipoFacturacion;
    
        return $this;
    }

    /**
     * Get tipoFacturacion
     *
     * @return string
     */
    public function getTipoFacturacion()
    {
        return $this->tipoFacturacion;
    }

    /**
     * Set calculaHExtra
     *
     * @param boolean $calculaHExtra
     *
     * @return FacturacionCliente
     */
    public function setCalculaHExtra($calculaHExtra)
    {
        $this->calculaHExtra = $calculaHExtra;
    
        return $this;
    }

    /**
     * Get calculaHExtra
     *
     * @return boolean
     */
    public function getCalculaHExtra()
    {
        return $this->calculaHExtra;
    }

    /**
     * Set importeHExtra
     *
     * @param float $importeHExtra
     *
     * @return FacturacionCliente
     */
    public function setImporteHExtra($importeHExtra)
    {
        $this->importeHExtra = $importeHExtra;
    
        return $this;
    }

    /**
     * Get importeHExtra
     *
     * @return float
     */
    public function getImporteHExtra()
    {
        return $this->importeHExtra;
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
     * Add tarifa
     *
     * @param \TarifaServicio $tarifa
     *
     * @return FacturacionCliente
     */
    public function addTarifa(\TarifaServicio $tarifa)
    {
        $this->tarifas[] = $tarifa;
    
        return $this;
    }

    /**
     * Remove tarifa
     *
     * @param \TarifaServicio $tarifa
     */
    public function removeTarifa(\TarifaServicio $tarifa)
    {
        $this->tarifas->removeElement($tarifa);
    }

    /**
     * Get tarifas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarifas()
    {
        return $this->tarifas;
    }

    /**
     * Set cliente
     *
     * @param \Cliente $cliente
     *
     * @return FacturacionCliente
     */
    public function setCliente(\Cliente $cliente = null)
    {
        $this->cliente = $cliente;
    
        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }
    /**
     * @var \ArticuloCliente
     */
    private $articulo;


    /**
     * Set articulo
     *
     * @param \ArticuloCliente $articulo
     * @return FacturacionCliente
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
     * @var string
     */
    private $nombre;


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return FacturacionCliente
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    public function existeCronograma($crono)
    {
        $existe = array('existe' => false);
        foreach ($this->tarifas as $tarifa) {
            if ($tarifa->existeCronograma($crono)){
                return array('existe'=>true, 'tarifa' => $tarifa);
            }
        }
        return $existe;
    }
}
