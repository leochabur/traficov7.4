<?php



/**
 * AccionUnidad
 */
class AccionUnidad
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
     * @var string
     */
    private $observaciones;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \TipoAccionUnidad
     */
    private $accion;

    /**
     * @var \Unidad
     */
    private $unidad;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $responsables;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responsables = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return AccionUnidad
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
     *
     * @return AccionUnidad
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
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return AccionUnidad
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
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
     * Set accion
     *
     * @param \TipoAccionUnidad $accion
     *
     * @return AccionUnidad
     */
    public function setAccion(\TipoAccionUnidad $accion = null)
    {
        $this->accion = $accion;
    
        return $this;
    }

    /**
     * Get accion
     *
     * @return \TipoAccionUnidad
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set unidad
     *
     * @param \Unidad $unidad
     *
     * @return AccionUnidad
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
     * Add responsable
     *
     * @param \Empleado $responsable
     *
     * @return AccionUnidad
     */
    public function addResponsable(\Empleado $responsable)
    {
        $this->responsables[] = $responsable;
    
        return $this;
    }

    /**
     * Remove responsable
     *
     * @param \Empleado $responsable
     */
    public function removeResponsable(\Empleado $responsable)
    {
        $this->responsables->removeElement($responsable);
    }

    /**
     * Get responsables
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsables()
    {
        return $this->responsables;
    }
    /**
     * @ORM\PrePersist
     */
    public function actualizarHorario()
    {
        // Add your code here
    }
}
