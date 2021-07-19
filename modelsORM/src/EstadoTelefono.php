<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoTelefono
 */
class EstadoTelefono
{
    /**
     * @var string
     */
    private $estado;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set estado
     *
     * @param string $estado
     * @return EstadoTelefono
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
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
}
