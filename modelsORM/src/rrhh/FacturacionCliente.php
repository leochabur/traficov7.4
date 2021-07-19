<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * FacturacionCliente
 */
class FacturacionCliente
{
    /**
     * @var string
     */
    private $nombre;

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

    /**
     * Set tipoFacturacion
     *
     * @param string $tipoFacturacion
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
     * Add tarifas
     *
     * @param \TarifaServicio $tarifas
     * @return FacturacionCliente
     */
    public function addTarifa(\TarifaServicio $tarifas)
    {
        $this->tarifas[] = $tarifas;

        return $this;
    }

    /**
     * Remove tarifas
     *
     * @param \TarifaServicio $tarifas
     */
    public function removeTarifa(\TarifaServicio $tarifas)
    {
        $this->tarifas->removeElement($tarifas);
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
     * @return FacturacionCliente
     */
    public function setCliente(\Cliente $cliente)
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
