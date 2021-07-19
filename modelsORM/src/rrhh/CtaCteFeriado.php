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
}
