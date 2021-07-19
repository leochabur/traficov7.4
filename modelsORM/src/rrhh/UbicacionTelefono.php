<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * UbicacionTelefono
 */
class UbicacionTelefono
{
    /**
     * @var string
     */
    private $ubicacion;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return UbicacionTelefono
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string 
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
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
