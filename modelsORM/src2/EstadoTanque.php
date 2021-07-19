<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoTanque
 */
class EstadoTanque
{
    /**
     * @var integer
     */
    private $disponible;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Unidad
     */
    private $unidad;

    private $mostrar;

    /**
     * Set disponible
     *
     * @param integer $disponible
     * @return EstadoTanque
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * Get disponible
     *
     * @return integer 
     */
    public function getDisponible()
    {
        return $this->disponible;
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
     * Set unidad
     *
     * @param \Unidad $unidad
     * @return EstadoTanque
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


    public function setMostrar($mostrar)
    {
        $this->mostrar = $mostrar;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Unidad 
     */
    public function getMostrar()
    {
        return $this->mostrar;
    }    
}
