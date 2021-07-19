<?php


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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifas;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifas = new \Doctrine\Common\Collections\ArrayCollection();
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
}
