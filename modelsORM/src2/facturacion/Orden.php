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
     * @var \DateTime
     */
    private $hcitacion;

    /**
     * @var \DateTime
     */
    private $hsalida;

    /**
     * @var \DateTime
     */
    private $hllegada;

    /**
     * @var \DateTime
     */
    private $hfina;

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
     * Set hcitacion
     *
     * @param \DateTime $hcitacion
     * @return Orden
     */
    public function setHcitacion($hcitacion)
    {
        $this->hcitacion = $hcitacion;

        return $this;
    }

    /**
     * Get hcitacion
     *
     * @return \DateTime 
     */
    public function getHcitacion()
    {
        return $this->hcitacion;
    }

    /**
     * Set hsalida
     *
     * @param \DateTime $hsalida
     * @return Orden
     */
    public function setHsalida($hsalida)
    {
        $this->hsalida = $hsalida;

        return $this;
    }

    /**
     * Get hsalida
     *
     * @return \DateTime 
     */
    public function getHsalida()
    {
        return $this->hsalida;
    }

    /**
     * Set hllegada
     *
     * @param \DateTime $hllegada
     * @return Orden
     */
    public function setHllegada($hllegada)
    {
        $this->hllegada = $hllegada;

        return $this;
    }

    /**
     * Get hllegada
     *
     * @return \DateTime 
     */
    public function getHllegada()
    {
        return $this->hllegada;
    }

    /**
     * Set hfina
     *
     * @param \DateTime $hfina
     * @return Orden
     */
    public function setHfina($hfina)
    {
        $this->hfina = $hfina;

        return $this;
    }

    /**
     * Get hfina
     *
     * @return \DateTime 
     */
    public function getHfina()
    {
        return $this->hfina;
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
