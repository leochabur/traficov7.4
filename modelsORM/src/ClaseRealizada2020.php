<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ClaseRealizada
 */
class ClaseRealizada
{
    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \ClaseCurso
     */
    private $clase;

    /**
     * @var \Empleado
     */
    private $empleado;


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return ClaseRealizada
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set clase
     *
     * @param \ClaseCurso $clase
     * @return ClaseRealizada
     */
    public function setClase(\ClaseCurso $clase = null)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return \ClaseCurso 
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * Set empleado
     *
     * @param \Empleado $empleado
     * @return ClaseRealizada
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
