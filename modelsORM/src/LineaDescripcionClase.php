<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * LineaDescripcionClase
 */
class LineaDescripcionClase
{
    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var float
     */
    private $orden;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \ClaseCurso
     */
    private $clase;


    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return LineaDescripcionClase
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
     * Set orden
     *
     * @param float $orden
     * @return LineaDescripcionClase
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return float 
     */
    public function getOrden()
    {
        return $this->orden;
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
     * @return LineaDescripcionClase
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
}
