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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $respuestas;

    /**
     * @var \ClaseAulaVirtual
     */
    private $clase;

    /**
     * @var \Empleado
     */
    private $empleado;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->respuestas = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Add respuestas
     *
     * @param \RespuestaPreguntaRealizada $respuestas
     * @return ClaseRealizada
     */
    public function addRespuesta(\RespuestaPreguntaRealizada $respuestas)
    {
        $this->respuestas[] = $respuestas;

        return $this;
    }

    /**
     * Remove respuestas
     *
     * @param \RespuestaPreguntaRealizada $respuestas
     */
    public function removeRespuesta(\RespuestaPreguntaRealizada $respuestas)
    {
        $this->respuestas->removeElement($respuestas);
    }

    /**
     * Get respuestas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRespuestas()
    {
        return $this->respuestas;
    }

    /**
     * Set clase
     *
     * @param \ClaseAulaVirtual $clase
     * @return ClaseRealizada
     */
    public function setClase(\ClaseAulaVirtual $clase = null)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return \ClaseAulaVirtual 
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
