<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * CargaCombustible
 */
class CargaCombustible
{
    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var \DateTime
     */
    private $fechaAlta;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Turno
     */
    private $turno;

    /**
     * @var \Unidad
     */
    private $unidad;


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return CargaCombustible
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
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return CargaCombustible
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
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
     * Set turno
     *
     * @param \Turno $turno
     * @return CargaCombustible
     */
    public function setTurno(\Turno $turno = null)
    {
        $this->turno = $turno;

        return $this;
    }

    /**
     * Get turno
     *
     * @return \Turno 
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * Set unidad
     *
     * @param \Unidad $unidad
     * @return CargaCombustible
     */
    public function setUnidad(\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Unidad 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
    /**
     * @ORM\PrePersist
     */
    public function actualizarHorario()
    {
        // Add your code here
    }
}
