<?php



use Doctrine\ORM\Mapping as ORM;

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
     * Add pagos
     *
     * @param \MovimientoCredito $pagos
     * @return MovimientoDebito
     */
    public function addPago(\MovimientoCredito $pagos)
    {
        $this->pagos[] = $pagos;

        return $this;
    }

    /**
     * Remove pagos
     *
     * @param \MovimientoCredito $pagos
     */
    public function removePago(\MovimientoCredito $pagos)
    {
        $this->pagos->removeElement($pagos);
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
