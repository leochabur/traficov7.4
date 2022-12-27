<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ReservaPasajero
 */
class ReservaPasajero
{
    /**
     * @var string
     */
    private $apellido;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $direccion;

    /**
     * @var string
     */
    private $ciudad;

    /**
     * @var integer
     */
    private $dni;

    /**
     * @var float
     */
    private $latitud;

    /**
     * @var float
     */
    private $longtud;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Pasajero
     */
    private $pasajero;

    /**
     * @var \OrdenGPX
     */
    private $ordenGPX;


    public function __toString()
    {
        return strtoupper($this->apellido.", ".$this->nombre);
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return ReservaPasajero
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    
        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return ReservaPasajero
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
     * Set direccion
     *
     * @param string $direccion
     * @return ReservaPasajero
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    
        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     * @return ReservaPasajero
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    
        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set dni
     *
     * @param integer $dni
     * @return ReservaPasajero
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    
        return $this;
    }

    /**
     * Get dni
     *
     * @return integer 
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set latitud
     *
     * @param float $latitud
     * @return ReservaPasajero
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;
    
        return $this;
    }

    /**
     * Get latitud
     *
     * @return float 
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longtud
     *
     * @param float $longtud
     * @return ReservaPasajero
     */
    public function setLongtud($longtud)
    {
        $this->longtud = $longtud;
    
        return $this;
    }

    /**
     * Get longtud
     *
     * @return float 
     */
    public function getLongtud()
    {
        return $this->longtud;
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
     * Set pasajero
     *
     * @param \Pasajero $pasajero
     * @return ReservaPasajero
     */
    public function setPasajero(\Pasajero $pasajero = null)
    {
        $this->pasajero = $pasajero;
    
        return $this;
    }

    /**
     * Get pasajero
     *
     * @return \Pasajero 
     */
    public function getPasajero()
    {
        return $this->pasajero;
    }

    /**
     * Set ordenGPX
     *
     * @param \OrdenGPX $ordenGPX
     * @return ReservaPasajero
     */
    public function setOrdenGPX(\OrdenGPX $ordenGPX = null)
    {
        $this->ordenGPX = $ordenGPX;
    
        return $this;
    }

    /**
     * Get ordenGPX
     *
     * @return \OrdenGPX 
     */
    public function getOrdenGPX()
    {
        return $this->ordenGPX;
    }
    /**
     * @var integer
     */
    private $idReservaPaxtracker;


    /**
     * Set idReservaPaxtracker
     *
     * @param integer $idReservaPaxtracker
     * @return ReservaPasajero
     */
    public function setIdReservaPaxtracker($idReservaPaxtracker)
    {
        $this->idReservaPaxtracker = $idReservaPaxtracker;
    
        return $this;
    }

    /**
     * Get idReservaPaxtracker
     *
     * @return integer 
     */
    public function getIdReservaPaxtracker()
    {
        return $this->idReservaPaxtracker;
    }
}
