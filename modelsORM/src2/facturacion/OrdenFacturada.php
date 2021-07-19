<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenFacturada
 */
class OrdenFacturada
{
    /**
     * @var float
     */
    private $importe;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Orden
     */
    private $orden;

    /**
     * @var \FacturaVenta
     */
    private $facturaVenta;


    /**
     * Set importe
     *
     * @param float $importe
     * @return OrdenFacturada
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orden
     *
     * @param \Orden $orden
     * @return OrdenFacturada
     */
    public function setOrden(\Orden $orden = null)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return \Orden 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set facturaVenta
     *
     * @param \FacturaVenta $facturaVenta
     * @return OrdenFacturada
     */
    public function setFacturaVenta(\FacturaVenta $facturaVenta = null)
    {
        $this->facturaVenta = $facturaVenta;

        return $this;
    }

    /**
     * Get facturaVenta
     *
     * @return \FacturaVenta 
     */
    public function getFacturaVenta()
    {
        return $this->facturaVenta;
    }
    /**
     * @var float
     */
    private $importeUnitario;

    /**
     * @var integer
     */
    private $cantidad;

    /**
     * @var \ArticuloCliente
     */
    private $articulo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ordenes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ordenes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set importeUnitario
     *
     * @param float $importeUnitario
     * @return OrdenFacturada
     */
    public function setImporteUnitario($importeUnitario)
    {
        $this->importeUnitario = $importeUnitario;

        return $this;
    }

    /**
     * Get importeUnitario
     *
     * @return float 
     */
    public function getImporteUnitario()
    {
        return $this->importeUnitario;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return OrdenFacturada
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set articulo
     *
     * @param \ArticuloCliente $articulo
     * @return OrdenFacturada
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
     * Add ordenes
     *
     * @param \Orden $ordenes
     * @return OrdenFacturada
     */
    public function addOrdene(\Orden $ordenes)
    {
        $this->ordenes[] = $ordenes;

        return $this;
    }

    /**
     * Remove ordenes
     *
     * @param \Orden $ordenes
     */
    public function removeOrdene(\Orden $ordenes)
    {
        $this->ordenes->removeElement($ordenes);
    }

    /**
     * Get ordenes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenes()
    {
        return $this->ordenes;
    }
    /**
     * @var \DateTime
     */
    private $fechaAlta;


    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return OrdenFacturada
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }
}
