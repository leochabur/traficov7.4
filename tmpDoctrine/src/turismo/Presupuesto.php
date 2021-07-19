<?php



/**
 * Presupuesto
 */
class Presupuesto
{
    /**
     * @var \DateTime
     */
    private $fechaSolicitud;

    /**
     * @var \DateTime
     */
    private $fechaConfeccion;

    /**
     * @var \DateTime
     */
    private $fechaInforme;

    /**
     * @var boolean
     */
    private $pagoAnticipado;

    /**
     * @var boolean
     */
    private $emiteComprobante;

    /**
     * @var boolean
     */
    private $eliminado;

    /**
     * @var boolean
     */
    private $confirmado;

    /**
     * @var integer
     */
    private $pax;

    /**
     * @var \DateTime
     */
    private $dateAction;

    /**
     * @var string
     */
    private $nombreContacto;

    /**
     * @var string
     */
    private $telefonoContacto;

    /**
     * @var string
     */
    private $mailContacto;

    /**
     * @var string
     */
    private $montoSIva;

    /**
     * @var string
     */
    private $iva;

    /**
     * @var string
     */
    private $montoFinal;

    /**
     * @var string
     */
    private $observaciones;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $viajes;

    /**
     * @var \Estructura
     */
    private $estructura;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * @var \Usuario
     */
    private $usuario;

    /**
     * @var \CanalPedido
     */
    private $canalPedido;

    /**
     * @var \EstadoPresupuesto
     */
    private $estado;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->viajes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set fechaSolicitud
     *
     * @param \DateTime $fechaSolicitud
     *
     * @return Presupuesto
     */
    public function setFechaSolicitud($fechaSolicitud)
    {
        $this->fechaSolicitud = $fechaSolicitud;
    
        return $this;
    }

    /**
     * Get fechaSolicitud
     *
     * @return \DateTime
     */
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }

    /**
     * Set fechaConfeccion
     *
     * @param \DateTime $fechaConfeccion
     *
     * @return Presupuesto
     */
    public function setFechaConfeccion($fechaConfeccion)
    {
        $this->fechaConfeccion = $fechaConfeccion;
    
        return $this;
    }

    /**
     * Get fechaConfeccion
     *
     * @return \DateTime
     */
    public function getFechaConfeccion()
    {
        return $this->fechaConfeccion;
    }

    /**
     * Set fechaInforme
     *
     * @param \DateTime $fechaInforme
     *
     * @return Presupuesto
     */
    public function setFechaInforme($fechaInforme)
    {
        $this->fechaInforme = $fechaInforme;
    
        return $this;
    }

    /**
     * Get fechaInforme
     *
     * @return \DateTime
     */
    public function getFechaInforme()
    {
        return $this->fechaInforme;
    }

    /**
     * Set pagoAnticipado
     *
     * @param boolean $pagoAnticipado
     *
     * @return Presupuesto
     */
    public function setPagoAnticipado($pagoAnticipado)
    {
        $this->pagoAnticipado = $pagoAnticipado;
    
        return $this;
    }

    /**
     * Get pagoAnticipado
     *
     * @return boolean
     */
    public function getPagoAnticipado()
    {
        return $this->pagoAnticipado;
    }

    /**
     * Set emiteComprobante
     *
     * @param boolean $emiteComprobante
     *
     * @return Presupuesto
     */
    public function setEmiteComprobante($emiteComprobante)
    {
        $this->emiteComprobante = $emiteComprobante;
    
        return $this;
    }

    /**
     * Get emiteComprobante
     *
     * @return boolean
     */
    public function getEmiteComprobante()
    {
        return $this->emiteComprobante;
    }

    /**
     * Set eliminado
     *
     * @param boolean $eliminado
     *
     * @return Presupuesto
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
     * Set confirmado
     *
     * @param boolean $confirmado
     *
     * @return Presupuesto
     */
    public function setConfirmado($confirmado)
    {
        $this->confirmado = $confirmado;
    
        return $this;
    }

    /**
     * Get confirmado
     *
     * @return boolean
     */
    public function getConfirmado()
    {
        return $this->confirmado;
    }

    /**
     * Set pax
     *
     * @param integer $pax
     *
     * @return Presupuesto
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
     * Set dateAction
     *
     * @param \DateTime $dateAction
     *
     * @return Presupuesto
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;
    
        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \DateTime
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Set nombreContacto
     *
     * @param string $nombreContacto
     *
     * @return Presupuesto
     */
    public function setNombreContacto($nombreContacto)
    {
        $this->nombreContacto = $nombreContacto;
    
        return $this;
    }

    /**
     * Get nombreContacto
     *
     * @return string
     */
    public function getNombreContacto()
    {
        return $this->nombreContacto;
    }

    /**
     * Set telefonoContacto
     *
     * @param string $telefonoContacto
     *
     * @return Presupuesto
     */
    public function setTelefonoContacto($telefonoContacto)
    {
        $this->telefonoContacto = $telefonoContacto;
    
        return $this;
    }

    /**
     * Get telefonoContacto
     *
     * @return string
     */
    public function getTelefonoContacto()
    {
        return $this->telefonoContacto;
    }

    /**
     * Set mailContacto
     *
     * @param string $mailContacto
     *
     * @return Presupuesto
     */
    public function setMailContacto($mailContacto)
    {
        $this->mailContacto = $mailContacto;
    
        return $this;
    }

    /**
     * Get mailContacto
     *
     * @return string
     */
    public function getMailContacto()
    {
        return $this->mailContacto;
    }

    /**
     * Set montoSIva
     *
     * @param string $montoSIva
     *
     * @return Presupuesto
     */
    public function setMontoSIva($montoSIva)
    {
        $this->montoSIva = $montoSIva;
    
        return $this;
    }

    /**
     * Get montoSIva
     *
     * @return string
     */
    public function getMontoSIva()
    {
        return $this->montoSIva;
    }

    /**
     * Set iva
     *
     * @param string $iva
     *
     * @return Presupuesto
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
    
        return $this;
    }

    /**
     * Get iva
     *
     * @return string
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set montoFinal
     *
     * @param string $montoFinal
     *
     * @return Presupuesto
     */
    public function setMontoFinal($montoFinal)
    {
        $this->montoFinal = $montoFinal;
    
        return $this;
    }

    /**
     * Get montoFinal
     *
     * @return string
     */
    public function getMontoFinal()
    {
        return $this->montoFinal;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Presupuesto
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add viaje
     *
     * @param \Viaje $viaje
     *
     * @return Presupuesto
     */
    public function addViaje(\Viaje $viaje)
    {
        $this->viajes[] = $viaje;

        $viaje->setPresupuesto($this);
    
        return $this;
    }

    /**
     * Remove viaje
     *
     * @param \Viaje $viaje
     */
    public function removeViaje(\Viaje $viaje)
    {
        $this->viajes->removeElement($viaje);
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

    /**
     * Set estructura
     *
     * @param \Estructura $estructura
     *
     * @return Presupuesto
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

    /**
     * Set cliente
     *
     * @param \Cliente $cliente
     *
     * @return Presupuesto
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

    /**
     * Set usuario
     *
     * @param \Usuario $usuario
     *
     * @return Presupuesto
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
     * Set canalPedido
     *
     * @param \CanalPedido $canalPedido
     *
     * @return Presupuesto
     */
    public function setCanalPedido(\CanalPedido $canalPedido = null)
    {
        $this->canalPedido = $canalPedido;
    
        return $this;
    }

    /**
     * Get canalPedido
     *
     * @return \CanalPedido
     */
    public function getCanalPedido()
    {
        return $this->canalPedido;
    }

    /**
     * Set estado
     *
     * @param \EstadoPresupuesto $estado
     *
     * @return Presupuesto
     */
    public function setEstado(\EstadoPresupuesto $estado = null)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return \EstadoPresupuesto
     */
    public function getEstado()
    {
        return $this->estado;
    }
    /**
     * @ORM\PrePersist
     */
    public function setPrePersistAction()
    {
               $this->eliminado = false;
               $this->confirmado = false;
               $this->numeroOrdenCompra = "";
               $this->facturado = false;
    }
    /**
     * @var boolean
     */
    private $confConOrdenCompra;

    /**
     * @var string
     */
    private $numeroOrdenCompra;


    /**
     * Set confConOrdenCompra
     *
     * @param boolean $confConOrdenCompra
     * @return Presupuesto
     */
    public function setConfConOrdenCompra($confConOrdenCompra)
    {
        $this->confConOrdenCompra = $confConOrdenCompra;

        return $this;
    }

    /**
     * Get confConOrdenCompra
     *
     * @return boolean 
     */
    public function getConfConOrdenCompra()
    {
        return $this->confConOrdenCompra;
    }

    /**
     * Set numeroOrdenCompra
     *
     * @param string $numeroOrdenCompra
     * @return Presupuesto
     */
    public function setNumeroOrdenCompra($numeroOrdenCompra)
    {
        $this->numeroOrdenCompra = $numeroOrdenCompra;

        return $this;
    }

    /**
     * Get numeroOrdenCompra
     *
     * @return string 
     */
    public function getNumeroOrdenCompra()
    {
        return $this->numeroOrdenCompra;
    }

    private $facturado;


    /**
     * Set facturado
     *
     * @param boolean $facturado
     * @return Presupuesto
     */
    public function setFacturado($facturado)
    {
        $this->facturado = $facturado;

        return $this;
    }

    /**
     * Get facturado
     *
     * @return boolean 
     */
    public function getFacturado()
    {
        return $this->facturado;
    }    

    /**
     * @var \DateTime
     */
    private $fechaFactura;

    /**
     * @var string
     */
    private $numeroFactura;

    /**
     * @var string
     */
    private $percepcion;

    /**
     * @var string
     */
    private $montoFinalMasPerc;


    /**
     * Set fechaFactura
     *
     * @param \DateTime $fechaFactura
     * @return Presupuesto
     */
    public function setFechaFactura($fechaFactura)
    {
        $this->fechaFactura = $fechaFactura;

        return $this;
    }

    /**
     * Get fechaFactura
     *
     * @return \DateTime 
     */
    public function getFechaFactura()
    {
        return $this->fechaFactura;
    }

    /**
     * Set numeroFactura
     *
     * @param string $numeroFactura
     * @return Presupuesto
     */
    public function setNumeroFactura($numeroFactura)
    {
        $this->numeroFactura = $numeroFactura;

        return $this;
    }

    /**
     * Get numeroFactura
     *
     * @return string 
     */
    public function getNumeroFactura()
    {
        return $this->numeroFactura;
    }

    /**
     * Set percepcion
     *
     * @param string $percepcion
     * @return Presupuesto
     */
    public function setPercepcion($percepcion)
    {
        $this->percepcion = $percepcion;

        return $this;
    }

    /**
     * Get percepcion
     *
     * @return string 
     */
    public function getPercepcion()
    {
        return $this->percepcion;
    }

    /**
     * Set montoFinalMasPerc
     *
     * @param string $montoFinalMasPerc
     * @return Presupuesto
     */
    public function setMontoFinalMasPerc($montoFinalMasPerc)
    {
        $this->montoFinalMasPerc = $montoFinalMasPerc;

        return $this;
    }

    /**
     * Get montoFinalMasPerc
     *
     * @return string 
     */
    public function getMontoFinalMasPerc()
    {
        return $this->montoFinalMasPerc;
    } 

    public function existeViaje (Viaje $viaje)
    {
        return $this->viajes->contains($viaje);
    }         
}
