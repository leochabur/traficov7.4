<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoCredito
 */
class MovimientoCredito
{
    /**
     * @var \MovimientoDebito
     */
    private $debito;

    /**
     * @var \MedioPago
     */
    private $medioPago;


    /**
     * Set debito
     *
     * @param \MovimientoDebito $debito
     * @return MovimientoCredito
     */
    public function setDebito(\MovimientoDebito $debito = null)
    {
        $this->debito = $debito;

        return $this;
    }

    /**
     * Get debito
     *
     * @return \MovimientoDebito 
     */
    public function getDebito()
    {
        return $this->debito;
    }

    /**
     * Set medioPago
     *
     * @param \MedioPago $medioPago
     * @return MovimientoCredito
     */
    public function setMedioPago(\MedioPago $medioPago = null)
    {
        $this->medioPago = $medioPago;

        return $this;
    }

    /**
     * Get medioPago
     *
     * @return \MedioPago 
     */
    public function getMedioPago()
    {
        return $this->medioPago;
    }
}
