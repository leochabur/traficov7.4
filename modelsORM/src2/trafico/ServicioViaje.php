<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ServicioViaje
 */
class ServicioViaje
{
    /**
     * @var string
     */
    private $servicio;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set servicio
     *
     * @param string $servicio
     * @return ServicioViaje
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return string 
     */
    public function getServicio()
    {
        return $this->servicio;
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
