<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenModificada
 */
class OrdenModificada
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $fservicio;

    /**
     * @var string
     */
    private $nombre;

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
    private $km;

    /**
     * @var boolean
     */
    private $finalizada;

    /**
     * @var boolean
     */
    private $borrada;

    /**
     * @var string
     */
    private $comentario;

    /**
     * @var boolean
     */
    private $vacio;

    /**
     * @var \DateTime
     */
    private $fechaAccion;

    /**
     * @var integer
     */
    private $pasajeros;

    /**
     * @var boolean
     */
    private $suspendida;

    /**
     * @var boolean
     */
    private $checkeada;

    /**
     * @var float
     */
    private $peajes;

    /**
     * @var \DateTime
     */
    private $hcitacionReal;

    /**
     * @var \DateTime
     */
    private $hsalidaPlantaReal;

    /**
     * @var \DateTime
     */
    private $hllegadaPlantaReal;

    /**
     * @var \DateTime
     */
    private $hfinservicioReal;

    /**
     * @var \Estructura
     */
    private $estructura;

    /**
     * @var \Servicio
     */
    private $servicio;

    /**
     * @var \Ciudad
     */
    private $origen;

    /**
     * @var \Ciudad
     */
    private $destino;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * @var \Cliente
     */
    private $clienteVacio;

    /**
     * @var \Empleado
     */
    private $conductor1;

    /**
     * @var \Empleado
     */
    private $conductor2;

    /**
     * @var \Unidad
     */
    private $unidad;

    /**
     * @var \usuario
     */
    private $usuario;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ordenesVacios;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $viajes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ordenesVacios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->viajes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return OrdenModificada
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
     * Set fservicio
     *
     * @param \DateTime $fservicio
     * @return OrdenModificada
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
     * Set nombre
     *
     * @param string $nombre
     * @return OrdenModificada
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
     * Set hcitacion
     *
     * @param \DateTime $hcitacion
     * @return OrdenModificada
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
     * @return OrdenModificada
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
     * @return OrdenModificada
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
     * @return OrdenModificada
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
     * Set km
     *
     * @param integer $km
     * @return OrdenModificada
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
     * Set finalizada
     *
     * @param boolean $finalizada
     * @return OrdenModificada
     */
    public function setFinalizada($finalizada)
    {
        $this->finalizada = $finalizada;

        return $this;
    }

    /**
     * Get finalizada
     *
     * @return boolean 
     */
    public function getFinalizada()
    {
        return $this->finalizada;
    }

    /**
     * Set borrada
     *
     * @param boolean $borrada
     * @return OrdenModificada
     */
    public function setBorrada($borrada)
    {
        $this->borrada = $borrada;

        return $this;
    }

    /**
     * Get borrada
     *
     * @return boolean 
     */
    public function getBorrada()
    {
        return $this->borrada;
    }

    /**
     * Set comentario
     *
     * @param string $comentario
     * @return OrdenModificada
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string 
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set vacio
     *
     * @param boolean $vacio
     * @return OrdenModificada
     */
    public function setVacio($vacio)
    {
        $this->vacio = $vacio;

        return $this;
    }

    /**
     * Get vacio
     *
     * @return boolean 
     */
    public function getVacio()
    {
        return $this->vacio;
    }

    /**
     * Set fechaAccion
     *
     * @param \DateTime $fechaAccion
     * @return OrdenModificada
     */
    public function setFechaAccion($fechaAccion)
    {
        $this->fechaAccion = $fechaAccion;

        return $this;
    }

    /**
     * Get fechaAccion
     *
     * @return \DateTime 
     */
    public function getFechaAccion()
    {
        return $this->fechaAccion;
    }

    /**
     * Set pasajeros
     *
     * @param integer $pasajeros
     * @return OrdenModificada
     */
    public function setPasajeros($pasajeros)
    {
        $this->pasajeros = $pasajeros;

        return $this;
    }

    /**
     * Get pasajeros
     *
     * @return integer 
     */
    public function getPasajeros()
    {
        return $this->pasajeros;
    }

    /**
     * Set suspendida
     *
     * @param boolean $suspendida
     * @return OrdenModificada
     */
    public function setSuspendida($suspendida)
    {
        $this->suspendida = $suspendida;

        return $this;
    }

    /**
     * Get suspendida
     *
     * @return boolean 
     */
    public function getSuspendida()
    {
        return $this->suspendida;
    }

    /**
     * Set checkeada
     *
     * @param boolean $checkeada
     * @return OrdenModificada
     */
    public function setCheckeada($checkeada)
    {
        $this->checkeada = $checkeada;

        return $this;
    }

    /**
     * Get checkeada
     *
     * @return boolean 
     */
    public function getCheckeada()
    {
        return $this->checkeada;
    }

    /**
     * Set peajes
     *
     * @param float $peajes
     * @return OrdenModificada
     */
    public function setPeajes($peajes)
    {
        $this->peajes = $peajes;

        return $this;
    }

    /**
     * Get peajes
     *
     * @return float 
     */
    public function getPeajes()
    {
        return $this->peajes;
    }

    /**
     * Set hcitacionReal
     *
     * @param \DateTime $hcitacionReal
     * @return OrdenModificada
     */
    public function setHcitacionReal($hcitacionReal)
    {
        $this->hcitacionReal = $hcitacionReal;

        return $this;
    }

    /**
     * Get hcitacionReal
     *
     * @return \DateTime 
     */
    public function getHcitacionReal()
    {
        return $this->hcitacionReal;
    }

    /**
     * Set hsalidaPlantaReal
     *
     * @param \DateTime $hsalidaPlantaReal
     * @return OrdenModificada
     */
    public function setHsalidaPlantaReal($hsalidaPlantaReal)
    {
        $this->hsalidaPlantaReal = $hsalidaPlantaReal;

        return $this;
    }

    /**
     * Get hsalidaPlantaReal
     *
     * @return \DateTime 
     */
    public function getHsalidaPlantaReal()
    {
        return $this->hsalidaPlantaReal;
    }

    /**
     * Set hllegadaPlantaReal
     *
     * @param \DateTime $hllegadaPlantaReal
     * @return OrdenModificada
     */
    public function setHllegadaPlantaReal($hllegadaPlantaReal)
    {
        $this->hllegadaPlantaReal = $hllegadaPlantaReal;

        return $this;
    }

    /**
     * Get hllegadaPlantaReal
     *
     * @return \DateTime 
     */
    public function getHllegadaPlantaReal()
    {
        return $this->hllegadaPlantaReal;
    }

    /**
     * Set hfinservicioReal
     *
     * @param \DateTime $hfinservicioReal
     * @return OrdenModificada
     */
    public function setHfinservicioReal($hfinservicioReal)
    {
        $this->hfinservicioReal = $hfinservicioReal;

        return $this;
    }

    /**
     * Get hfinservicioReal
     *
     * @return \DateTime 
     */
    public function getHfinservicioReal()
    {
        return $this->hfinservicioReal;
    }

    /**
     * Set estructura
     *
     * @param \Estructura $estructura
     * @return OrdenModificada
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
     * Set servicio
     *
     * @param \Servicio $servicio
     * @return OrdenModificada
     */
    public function setServicio(\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \Servicio 
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set origen
     *
     * @param \Ciudad $origen
     * @return OrdenModificada
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
     * @return OrdenModificada
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
     * Set cliente
     *
     * @param \Cliente $cliente
     * @return OrdenModificada
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
     * Set clienteVacio
     *
     * @param \Cliente $clienteVacio
     * @return OrdenModificada
     */
    public function setClienteVacio(\Cliente $clienteVacio = null)
    {
        $this->clienteVacio = $clienteVacio;

        return $this;
    }

    /**
     * Get clienteVacio
     *
     * @return \Cliente 
     */
    public function getClienteVacio()
    {
        return $this->clienteVacio;
    }

    /**
     * Set conductor1
     *
     * @param \Empleado $conductor1
     * @return OrdenModificada
     */
    public function setConductor1(\Empleado $conductor1 = null)
    {
        $this->conductor1 = $conductor1;

        return $this;
    }

    /**
     * Get conductor1
     *
     * @return \Empleado 
     */
    public function getConductor1()
    {
        return $this->conductor1;
    }

    /**
     * Set conductor2
     *
     * @param \Empleado $conductor2
     * @return OrdenModificada
     */
    public function setConductor2(\Empleado $conductor2 = null)
    {
        $this->conductor2 = $conductor2;

        return $this;
    }

    /**
     * Get conductor2
     *
     * @return \Empleado 
     */
    public function getConductor2()
    {
        return $this->conductor2;
    }

    /**
     * Set unidad
     *
     * @param \Unidad $unidad
     * @return OrdenModificada
     */
    public function setUnidad(\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Unidad 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set usuario
     *
     * @param \usuario $usuario
     * @return OrdenModificada
     */
    public function setUsuario(\usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Add ordenesVacios
     *
     * @param \Orden $ordenesVacios
     * @return OrdenModificada
     */
    public function addOrdenesVacio(\Orden $ordenesVacios)
    {
        $this->ordenesVacios[] = $ordenesVacios;

        return $this;
    }

    /**
     * Remove ordenesVacios
     *
     * @param \Orden $ordenesVacios
     */
    public function removeOrdenesVacio(\Orden $ordenesVacios)
    {
        $this->ordenesVacios->removeElement($ordenesVacios);
    }

    /**
     * Get ordenesVacios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenesVacios()
    {
        return $this->ordenesVacios;
    }

    /**
     * Add viajes
     *
     * @param \Viaje $viajes
     * @return OrdenModificada
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
    /**
     * @var \Orden
     */
    private $orden;


    /**
     * Set orden
     *
     * @param \Orden $orden
     * @return OrdenModificada
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
}
