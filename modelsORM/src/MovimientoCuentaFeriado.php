<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoCuentaFeriado
 */
abstract class MovimientoCuentaFeriado
{
    /**
     * @var \DateTime
     */
    private $fechaCarga;

    /**
     * @var integer
     */
    private $periodoMes;

    /**
     * @var integer
     */
    private $periodoAnio;

    /**
     * @var integer
     */
    private $cantidad;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Estructura
     */
    private $estructura;

    /**
     * @var \Feriado
     */
    private $feriadoAsociado;


    private $aplicado = false;


    public abstract function getFrancoCredito();
    public abstract function getFrancoDebito();
    public abstract function getFeriadoCredito();
    public abstract function getFeriadoDebito();
    public abstract function getCreditoACompensar();
    public abstract function getDebitosACompensar();


    public function setAplicado($aplicado)
    {
        $this->aplicado = $aplicado;

        return $this;
    }

    public function getAplicado()
    {
        return $this->aplicado;
    }


    /**
     * Set fechaCarga
     *
     * @param \DateTime $fechaCarga
     * @return MovimientoCuentaFeriado
     */
    public function setFechaCarga($fechaCarga)
    {
        $this->fechaCarga = $fechaCarga;

        return $this;
    }

    /**
     * Get fechaCarga
     *
     * @return \DateTime 
     */
    public function getFechaCarga()
    {
        return $this->fechaCarga;
    }

    /**
     * Set periodoMes
     *
     * @param integer $periodoMes
     * @return MovimientoCuentaFeriado
     */
    public function setPeriodoMes($periodoMes)
    {
        $this->periodoMes = $periodoMes;

        return $this;
    }

    /**
     * Get periodoMes
     *
     * @return integer 
     */
    public function getPeriodoMes()
    {
        return $this->periodoMes;
    }

    /**
     * Set periodoAnio
     *
     * @param integer $periodoAnio
     * @return MovimientoCuentaFeriado
     */
    public function setPeriodoAnio($periodoAnio)
    {
        $this->periodoAnio = $periodoAnio;

        return $this;
    }

    /**
     * Get periodoAnio
     *
     * @return integer 
     */
    public function getPeriodoAnio()
    {
        return $this->periodoAnio;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return MovimientoCuentaFeriado
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return MovimientoCuentaFeriado
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
     * Set estructura
     *
     * @param \Estructura $estructura
     * @return MovimientoCuentaFeriado
     */
    public function setEstructura(\Estructura $estructura = null)
    {
        $this->estructura = $estructura;

        return $this;
    }

    /**
     * Get estructura
     *
     * @return \Estructura 
     */
    public function getEstructura()
    {
        return $this->estructura;
    }

    /**
     * Set feriadoAsociado
     *
     * @param \Feriado $feriadoAsociado
     * @return MovimientoCuentaFeriado
     */
    public function setFeriadoAsociado(\Feriado $feriadoAsociado = null)
    {
        $this->feriadoAsociado = $feriadoAsociado;

        return $this;
    }

    /**
     * Get feriadoAsociado
     *
     * @return \Feriado 
     */
    public function getFeriadoAsociado()
    {
        return $this->feriadoAsociado;
    }
    /**
     * @var \CtaCteFeriado
     */
    private $ctacte;


    /**
     * Set ctacte
     *
     * @param \CtaCteFeriado $ctacte
     * @return MovimientoCuentaFeriado
     */
    public function setCtacte(\CtaCteFeriado $ctacte = null)
    {
        $this->ctacte = $ctacte;

        return $this;
    }

    /**
     * Get ctacte
     *
     * @return \CtaCteFeriado 
     */
    public function getCtacte()
    {
        return $this->ctacte;
    }
    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var boolean
     */
    private $activo = true;


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return MovimientoCuentaFeriado
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return MovimientoCuentaFeriado
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }
    /**
     * @var boolean
     */
    private $compensable = false;


    /**
     * Set compensable
     *
     * @param boolean $compensable
     * @return MovimientoCuentaFeriado
     */
    public function setCompensable($compensable)
    {
        $this->compensable = $compensable;

        return $this;
    }

    /**
     * Get compensable
     *
     * @return boolean 
     */
    public function getCompensable()
    {
        return $this->compensable;
    }
    /**
     * @var boolean
     */
    private $compensado = false;


    /**
     * Set compensado
     *
     * @param boolean $compensado
     * @return MovimientoCuentaFeriado
     */
    public function setCompensado($compensado)
    {
        $this->compensado = $compensado;

        return $this;
    }

    /**
     * Get compensado
     *
     * @return boolean 
     */
    public function getCompensado()
    {
        return $this->compensado;
    }
}
