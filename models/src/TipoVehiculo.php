<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TipoVehiculo
 */
class TipoVehiculo
{
    /**
     * @var string
     */
    private $tipo;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Estructura
     */
    private $estructura;


    /**
     * Set tipo
     *
     * @param string $tipo
     * @return TipoVehiculo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
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
     * @return TipoVehiculo
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
}
