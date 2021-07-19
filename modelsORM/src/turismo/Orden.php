<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Orden
 */
class Orden
{
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var \DateTime
     */
    private $fservicio;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $viajes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->viajes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Orden
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set fservicio
     *
     * @param \DateTime $fservicio
     * @return Orden
     */
    public function setFservicio($fservicio)
    {
        $this->fservicio = $fservicio;

        return $this;
    }

    /**
     * Get fservicio
     *
     * @return \DateTime 
     */
    public function getFservicio()
    {
        return $this->fservicio;
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
     * Add viajes
     *
     * @param \Viaje $viajes
     * @return Orden
     */
    public function addViaje(\Viaje $viajes)
    {
        $this->viajes[] = $viajes;

        return $this;
    }

    /**
     * Remove viajes
     *
     * @param \Viaje $viajes
     */
    public function removeViaje(\Viaje $viajes)
    {
        $this->viajes->removeElement($viajes);
    }

    /**
     * Get viajes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViajes()
    {
        return $this->viajes;
    }
}
