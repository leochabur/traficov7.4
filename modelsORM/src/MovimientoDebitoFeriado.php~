<?php


use Doctrine\ORM\Mapping as ORM;
include_once('MovimientoCuentaFeriado.php');

/**
 * MovimientoDebitoFeriado
 */
class MovimientoDebitoFeriado extends MovimientoCuentaFeriado
{
    /**
     * @var \Novedad
     */
    private $novedad;


    public function getFrancoCredito()
    {
        return 0;
    }

    public function getFrancoDebito()
    {
        if ($this->getActivo())
        {
            return ($this->novedad->getNovedadTexto()->getIsFranco()?$this->getCantidad():0);
        }
        else
            return 0;
    }

    public function getFeriadoCredito()
    {
        return 0;
    }

    public function getFeriadoDebito()
    {
        if ($this->getActivo())
        {
            return ($this->novedad->getNovedadTexto()->getIsFeriado()?$this->getCantidad():0);
        }
        else
            return 0;
    }

    public function getCreditoACompensar()
    {
        return 0;
    }

    public function getDebitosACompensar()
    {
        if ($this->getActivo())
        {
            return (($this->novedad->getNovedadTexto()->getCompensatorio() && (!$this->getAplicado()))?$this->getCantidad():0);
        }
        else
            return 0;

    }
    
    /**
     * Set novedad
     *
     * @param \Novedad $novedad
     * @return MovimientoDebitoFeriado
     */
    public function setNovedad(\Novedad $novedad = null)
    {
        $this->novedad = $novedad;

        return $this;
    }

    /**
     * Get novedad
     *
     * @return \Novedad 
     */
    public function getNovedad()
    {
        return $this->novedad;
    }
    /**
     * @var \MovimientoDebitoFeriado
     */
    private $debitoOrigen;


    /**
     * Set debitoOrigen
     *
     * @param \MovimientoDebitoFeriado $debitoOrigen
     * @return MovimientoDebitoFeriado
     */
    public function setDebitoOrigen(\MovimientoDebitoFeriado $debitoOrigen = null)
    {
        $this->debitoOrigen = $debitoOrigen;

        return $this;
    }

    /**
     * Get debitoOrigen
     *
     * @return \MovimientoDebitoFeriado 
     */
    public function getDebitoOrigen()
    {
        return $this->debitoOrigen;
    }
}
