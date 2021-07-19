<?php



/**
 * MovimientoDebito
 */
class MovimientoDebito
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pagos;

    /**
     * @var \Presupuesto
     */
    private $presupuesto;

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
}
