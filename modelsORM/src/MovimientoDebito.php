<?php
include_once('MovimientoCuenta.php');
class MovimientoDebito extends MovimientoCuenta
{
    /**
     * @var string
     */
    private $concepto;

    /**
     * @var integer
     */
    private $id;

    private $presupuesto;

    private $pagos;


    /**
     * Set concepto
     *
     * @param string $concepto
     *
     * @return MovimientoDebito
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;
    
        return $this;
    }

    /**
     * Get concepto
     *
     * @return string
     */
    public function getConcepto()
    {
        return $this->concepto;
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
     * Set presupuesto
     *
     * @param \Presupuesto $presupuesto
     *
     * @return MovimientoDebito
     */
    public function setPresupuesto(\Presupuesto $presupuesto = null)
    {
        $this->presupuesto = $presupuesto;
    
        return $this;
    }

    /**
     * Get presupuesto
     *
     * @return \Presupuesto
     */
    public function getPresupuesto()
    {
        return $this->presupuesto;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pagos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pago
     *
     * @param \MovimientoCredito $pago
     *
     * @return MovimientoDebito
     */
    public function addPago(\MovimientoCredito $pago)
    {
        $this->pagos[] = $pago;
    
        return $this;
    }

    /**
     * Remove pago
     *
     * @param \MovimientoCredito $pago
     */
    public function removePago(\MovimientoCredito $pago)
    {
        $this->pagos->removeElement($pago);
    }

    /**
     * Get pagos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagos()
    {
        return $this->pagos;
    }

    public function getSaldo($monto)
    {
        return ($monto + $this->getImporte());

    }     

    public function getIdent()
    {
        return parent::getId();
    } 

    public function getCredito()
    {
        return null;
    }

    public function getDebitos()
    {
        return $this->getImporte();
    }             
}
