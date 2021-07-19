<?php



/**
 * Viaje
 */
class Viaje
{
    /**
     * @var string
     */
    private $lugarSalida;

    /**
     * @var string
     */
    private $lugarLlegada;

    /**
     * @var \DateTime
     */
    private $hSalida;

    /**
     * @var \DateTime
     */
    private $hLlegada;

    /**
     * @var integer
     */
    private $pax;

    /**
     * @var integer
     */
    private $km;

    /**
     * @var string
     */
    private $observaciones;

    /**
     * @var \DateTime
     */
    private $hSalidaRegreso;

    /**
     * @var \DateTime
     */
    private $hLlegadaRegreso;

    /**
     * @var \DateTime
     */
    private $fSalida;

    /**
     * @var \DateTime
     */
    private $fRegreso;

    /**
     * @var boolean
     */
    private $eliminado = false;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Ciudad
     */
    private $origen;

    /**
     * @var \Ciudad
     */
    private $destino;

    /**
     * @var \Presupuesto
     */
    private $presupuesto;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $servicios;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->servicios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ordenes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set lugarSalida
     *
     * @param string $lugarSalida
     *
     * @return Viaje
     */
    public function setLugarSalida($lugarSalida)
    {
        $this->lugarSalida = $lugarSalida;
    
        return $this;
    }

    /**
     * Get lugarSalida
     *
     * @return string
     */
    public function getLugarSalida()
    {
        return $this->lugarSalida;
    }

    /**
     * Set lugarLlegada
     *
     * @param string $lugarLlegada
     *
     * @return Viaje
     */
    public function setLugarLlegada($lugarLlegada)
    {
        $this->lugarLlegada = $lugarLlegada;
    
        return $this;
    }

    /**
     * Get lugarLlegada
     *
     * @return string
     */
    public function getLugarLlegada()
    {
        return $this->lugarLlegada;
    }

    /**
     * Set hSalida
     *
     * @param \DateTime $hSalida
     *
     * @return Viaje
     */
    public function setHSalida($hSalida)
    {
        $this->hSalida = $hSalida;
    
        return $this;
    }

    /**
     * Get hSalida
     *
     * @return \DateTime
     */
    public function getHSalida()
    {
        return $this->hSalida;
    }

    /**
     * Set hLlegada
     *
     * @param \DateTime $hLlegada
     *
     * @return Viaje
     */
    public function setHLlegada($hLlegada)
    {
        $this->hLlegada = $hLlegada;
    
        return $this;
    }

    /**
     * Get hLlegada
     *
     * @return \DateTime
     */
    public function getHLlegada()
    {
        return $this->hLlegada;
    }

    /**
     * Set pax
     *
     * @param integer $pax
     *
     * @return Viaje
     */
    public function setPax($pax)
    {
        $this->pax = $pax;
    
        return $this;
    }

    /**
     * Get pax
     *
     * @return integer
     */
    public function getPax()
    {
        return $this->pax;
    }

    /**
     * Set km
     *
     * @param integer $km
     *
     * @return Viaje
     */
    public function setKm($km)
    {
        $this->km = $km;
    
        return $this;
    }

    /**
     * Get km
     *
     * @return integer
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Viaje
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set hSalidaRegreso
     *
     * @param \DateTime $hSalidaRegreso
     *
     * @return Viaje
     */
    public function setHSalidaRegreso($hSalidaRegreso)
    {
        $this->hSalidaRegreso = $hSalidaRegreso;
    
        return $this;
    }

    /**
     * Get hSalidaRegreso
     *
     * @return \DateTime
     */
    public function getHSalidaRegreso()
    {
        return $this->hSalidaRegreso;
    }

    /**
     * Set hLlegadaRegreso
     *
     * @param \DateTime $hLlegadaRegreso
     *
     * @return Viaje
     */
    public function setHLlegadaRegreso($hLlegadaRegreso)
    {
        $this->hLlegadaRegreso = $hLlegadaRegreso;
    
        return $this;
    }

    /**
     * Get hLlegadaRegreso
     *
     * @return \DateTime
     */
    public function getHLlegadaRegreso()
    {
        return $this->hLlegadaRegreso;
    }

    /**
     * Set fSalida
     *
     * @param \DateTime $fSalida
     *
     * @return Viaje
     */
    public function setFSalida($fSalida)
    {
        $this->fSalida = $fSalida;
    
        return $this;
    }

    /**
     * Get fSalida
     *
     * @return \DateTime
     */
    public function getFSalida()
    {
        return $this->fSalida;
    }

    /**
     * Set fRegreso
     *
     * @param \DateTime $fRegreso
     *
     * @return Viaje
     */
    public function setFRegreso($fRegreso)
    {
        $this->fRegreso = $fRegreso;
    
        return $this;
    }

    /**
     * Get fRegreso
     *
     * @return \DateTime
     */
    public function getFRegreso()
    {
        return $this->fRegreso;
    }

    /**
     * Set eliminado
     *
     * @param boolean $eliminado
     *
     * @return Viaje
     */
    public function setEliminado($eliminado)
    {
        $this->eliminado = $eliminado;
    
        return $this;
    }

    /**
     * Get eliminado
     *
     * @return boolean
     */
    public function getEliminado()
    {
        return $this->eliminado;
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
     * Set origen
     *
     * @param \Ciudad $origen
     *
     * @return Viaje
     */
    public function setOrigen(\Ciudad $origen = null)
    {
        $this->origen = $origen;
    
        return $this;
    }

    /**
     * Get origen
     *
     * @return \Ciudad
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set destino
     *
     * @param \Ciudad $destino
     *
     * @return Viaje
     */
    public function setDestino(\Ciudad $destino = null)
    {
        $this->destino = $destino;
    
        return $this;
    }

    /**
     * Get destino
     *
     * @return \Ciudad
     */
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Set presupuesto
     *
     * @param \Presupuesto $presupuesto
     *
     * @return Viaje
     */
    public function setPresupuesto(\Presupuesto $presupuesto = null)
    {
        $this->presupuesto = $presupuesto;
    
        return $this;
    }

    /**
     * Get presupuesto
     *
     * @return \Presupuesto
     */
    public function getPresupuesto()
    {
        return $this->presupuesto;
    }

    /**
     * Add servicio
     *
     * @param \ServicioViaje $servicio
     *
     * @return Viaje
     */
    public function addServicio(\ServicioViaje $servicio)
    {
        $this->servicios[] = $servicio;
    
        return $this;
    }

    /**
     * Remove servicio
     *
     * @param \ServicioViaje $servicio
     */
    public function removeServicio(\ServicioViaje $servicio)
    {
        $this->servicios->removeElement($servicio);
    }

    /**
     * Get servicios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServicios()
    {
        return $this->servicios;
    }
    /**
     * @ORM\PrePersist
     */
    public function setPreUpdateAction()
    {
        // Add your code here
    }
    /**
     * @var \DateTime
     */
    private $fLlegadaIda;

    /**
     * @var \DateTime
     */
    private $fLlegadaVuelta;


    /**
     * Set fLlegadaIda
     *
     * @param \DateTime $fLlegadaIda
     * @return Viaje
     */
    public function setFLlegadaIda($fLlegadaIda)
    {
        $this->fLlegadaIda = $fLlegadaIda;

        return $this;
    }

    /**
     * Get fLlegadaIda
     *
     * @return \DateTime 
     */
    public function getFLlegadaIda()
    {
        return $this->fLlegadaIda;
    }

    /**
     * Set fLlegadaVuelta
     *
     * @param \DateTime $fLlegadaVuelta
     * @return Viaje
     */
    public function setFLlegadaVuelta($fLlegadaVuelta)
    {
        $this->fLlegadaVuelta = $fLlegadaVuelta;

        return $this;
    }

    /**
     * Get fLlegadaVuelta
     *
     * @return \DateTime 
     */
    public function getFLlegadaVuelta()
    {
        return $this->fLlegadaVuelta;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ordenes;


    /**
     * Add ordenes
     *
     * @param \Orden $ordenes
     * @return Viaje
     */
    public function addOrdene(\Orden $ordenes)
    {
        $this->ordenes[] = $ordenes;

        return $this;
    }

    /**
     * Remove ordenes
     *
     * @param \Orden $ordenes
     */
    public function removeOrdene(\Orden $ordenes)
    {
        $this->ordenes->removeElement($ordenes);
    }

    /**
     * Get ordenes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenes()
    {
        return $this->ordenes;
    }
    /**
     * @var \Cliente
     */
    private $cliente;


    /**
     * Set cliente
     *
     * @param \Cliente $cliente
     * @return Viaje
     */
    public function setCliente(\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}
