<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaVenta
 */
class FacturaVenta
{
    /**
     * @var \DateTime
     */
    private $desde;

    /**
     * @var \DateTime
     */
    private $hasta;

    /**
     * @var boolean
     */
    private $cerrada = false;

    /**
     * @var float
     */
    private $importeNeto;

    /**
     * @var float
     */
    private $importeIva;

    /**
     * @var float
     */
    private $importeRetenciones;

    /**
     * @var float
     */
    private $importeTotal;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ordenesFacturadas;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ordenesFacturadas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set desde
     *
     * @param \DateTime $desde
     * @return FacturaVenta
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;

        return $this;
    }

    /**
     * Get desde
     *
     * @return \DateTime 
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     * @return FacturaVenta
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;

        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime 
     */
    public function getHasta()
    {
        return $this->hasta;
    }

    /**
     * Set cerrada
     *
     * @param boolean $cerrada
     * @return FacturaVenta
     */
    public function setCerrada($cerrada)
    {
        $this->cerrada = $cerrada;

        return $this;
    }

    /**
     * Get cerrada
     *
     * @return boolean 
     */
    public function getCerrada()
    {
        return $this->cerrada;
    }

    /**
     * Set importeNeto
     *
     * @param float $importeNeto
     * @return FacturaVenta
     */
    public function setImporteNeto($importeNeto)
    {
        $this->importeNeto = $importeNeto;

        return $this;
    }

    /**
     * Get importeNeto
     *
     * @return float 
     */
    public function getImporteNeto()
    {
        return $this->importeNeto;
    }

    /**
     * Set importeIva
     *
     * @param float $importeIva
     * @return FacturaVenta
     */
    public function setImporteIva($importeIva)
    {
        $this->importeIva = $importeIva;

        return $this;
    }

    /**
     * Get importeIva
     *
     * @return float 
     */
    public function getImporteIva()
    {
        return $this->importeIva;
    }

    /**
     * Set importeRetenciones
     *
     * @param float $importeRetenciones
     * @return FacturaVenta
     */
    public function setImporteRetenciones($importeRetenciones)
    {
        $this->importeRetenciones = $importeRetenciones;

        return $this;
    }

    /**
     * Get importeRetenciones
     *
     * @return float 
     */
    public function getImporteRetenciones()
    {
        return $this->importeRetenciones;
    }

    /**
     * Set importeTotal
     *
     * @param float $importeTotal
     * @return FacturaVenta
     */
    public function setImporteTotal($importeTotal)
    {
        $this->importeTotal = $importeTotal;

        return $this;
    }

    /**
     * Get importeTotal
     *
     * @return float 
     */
    public function getImporteTotal()
    {
        return $this->importeTotal;
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
     * Add ordenesFacturadas
     *
     * @param \OrdenFacturada $ordenesFacturadas
     * @return FacturaVenta
     */
    public function addOrdenesFacturada(\OrdenFacturada $ordenesFacturadas)
    {
        $this->ordenesFacturadas[] = $ordenesFacturadas;

        return $this;
    }

    /**
     * Remove ordenesFacturadas
     *
     * @param \OrdenFacturada $ordenesFacturadas
     */
    public function removeOrdenesFacturada(\OrdenFacturada $ordenesFacturadas)
    {
        $this->ordenesFacturadas->removeElement($ordenesFacturadas);
    }

    /**
     * Get ordenesFacturadas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenesFacturadas()
    {
        return $this->ordenesFacturadas;
    }

    /**
     * Set cliente
     *
     * @param \Cliente $cliente
     * @return FacturaVenta
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

    public function getMontoFactura()
    {
        $total = 0;
        foreach ($this->ordenesFacturadas as $orden) {
            # code...
            $total += ($orden->getImporteUnitario() * $orden->getOrdenes()->count());
        }
        return $total;
    }
    /**
     * @var string
     */
    private $descripcion;

    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return FacturaVenta
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
}
