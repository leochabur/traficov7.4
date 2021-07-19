<?php

use Doctrine\ORM\Mapping as ORM;
include_once('MovimientoCuentaFeriado.php');
/**
 * MovimientoCreditoFeriado
 */
class MovimientoCreditoFeriado extends MovimientoCuentaFeriado
{
    /**
     * @var \NovedadTexto
     */
    private $novedadTexto;


    public function getFrancoCredito()
    {
        if ($this->getActivo())
        {
            return ($this->novedadTexto->getIsFranco()?$this->getCantidad():0);
        }
        else
            return 0;
    }

    public function getFrancoDebito()
    {
        
    }

    public function getFeriadoCredito()
    {
        if ($this->getActivo())
        {
            return ($this->novedadTexto->getIsFeriado()?$this->getCantidad():0);
        }
        else
            return 0;
    }

    public function getFeriadoDebito()
    {
        return 0;
    }

    public function getCreditoACompensar()
    {
        if ($this->getActivo())
        {
            return ($this->novedadTexto->getCompensatorio()?$this->getCantidad():0);
        }
        else
            return 0;
    }

    public function getDebitosACompensar()
    {
        return 0;
    }

    /**
     * Set novedadTexto
     *
     * @param \NovedadTexto $novedadTexto
     * @return MovimientoCreditoFeriado
     */
    public function setNovedadTexto(\NovedadTexto $novedadTexto = null)
    {
        $this->novedadTexto = $novedadTexto;

        return $this;
    }

    /**
     * Get novedadTexto
     *
     * @return \NovedadTexto 
     */
    public function getNovedadTexto()
    {
        return $this->novedadTexto;
    }
}
