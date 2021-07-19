<?php



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
     * Set debito
     *
     * @param \MovimientoDebito $debito
     *
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
}
