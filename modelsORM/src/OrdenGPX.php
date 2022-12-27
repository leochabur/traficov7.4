<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenGPX
 */
class OrdenGPX
{
    /**
     * @var \DateTime
     */
    private $fechaComunicacion;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Orden
     */
    private $orden;

    /**
     * @var \Usuario
     */
    private $usuario;


    /**
     * Set fechaComunicacion
     *
     * @param \DateTime $fechaComunicacion
     * @return OrdenGPX
     */
    public function setFechaComunicacion($fechaComunicacion)
    {
        $this->fechaComunicacion = $fechaComunicacion;
    
        return $this;
    }

    /**
     * Get fechaComunicacion
     *
     * @return \DateTime 
     */
    public function getFechaComunicacion()
    {
        return $this->fechaComunicacion;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return OrdenGPX
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
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
     * Set orden
     *
     * @param \Orden $orden
     * @return OrdenGPX
     */
    public function setOrden(\Orden $orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return \Orden 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set usuario
     *
     * @param \Usuario $usuario
     * @return OrdenGPX
     */
    public function setUsuario(\Usuario $usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pasajeros;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pasajeros = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pasajeros
     *
     * @param \ReservaPasajero $pasajeros
     * @return OrdenGPX
     */
    public function addPasajero(\ReservaPasajero $pasajeros)
    {
        $this->pasajeros[] = $pasajeros;
    
        return $this;
    }

    /**
     * Remove pasajeros
     *
     * @param \ReservaPasajero $pasajeros
     */
    public function removePasajero(\ReservaPasajero $pasajeros)
    {
        $this->pasajeros->removeElement($pasajeros);
    }

    /**
     * Get pasajeros
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPasajeros()
    {
        return $this->pasajeros;
    }
}
