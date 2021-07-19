<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * CtaCteFeriado
 */
class CtaCteFeriado
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Empleado
     */
    private $empleado;


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
     * Set empleado
     *
     * @param \Empleado $empleado
     * @return CtaCteFeriado
     */
    public function setEmpleado(\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \Empleado 
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $movimientos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movimientos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add movimientos
     *
     * @param \MovimientoCuentaFeriado $movimientos
     * @return CtaCteFeriado
     */
    public function addMovimiento(\MovimientoCuentaFeriado $movimientos)
    {
        $this->movimientos[] = $movimientos;

        return $this;
    }

    /**
     * Remove movimientos
     *
     * @param \MovimientoCuentaFeriado $movimientos
     */
    public function removeMovimiento(\MovimientoCuentaFeriado $movimientos)
    {
        $this->movimientos->removeElement($movimientos);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }
}
