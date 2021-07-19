<?php

class MovimientoCredito extends MovimientoCuenta
{
    /**
     * @var string
     */
    private $conceptos;

    /**
     * @var integer
     */
    private $id;

    private $debito;


    /**
     * Set conceptos
     *
     * @param string $conceptos
     *
     * @return MovimientoCredito
     */
    public function setConceptos($conceptos)
    {
        $this->conceptos = $conceptos;
    
        return $this;
    }

    /**
     * Get conceptos
     *
     * @return string
     */
    public function getConceptos()
    {
        return $this->conceptos;
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
     * @return MovimientoCredito
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

    public function getSaldo($monto)
    {
        return ($monto-$this->getImporte());

    } 

    public function getIdent()
    {
        return parent::getId();
    } 

    public function getCredito()
    {
        return $this->getImporte();
    }

    public function getDebitos()
    {
        return null;
    }              
    /**
     * @var \MedioPago
     */
    private $medioPago;


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
