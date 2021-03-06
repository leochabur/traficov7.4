<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Cronograma
 */
class Cronograma
{
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var boolean
     */
    private $vacio;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Estructura
     */
    private $estructura;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * @var \Ciudad
     */
    private $origen;

    /**
     * @var \Ciudad
     */
    private $destino;


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Cronograma
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
     * Set activo
     *
     * @param boolean $activo
     * @return Cronograma
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set vacio
     *
     * @param boolean $vacio
     * @return Cronograma
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
     * @return Cronograma
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
     * @return Cronograma
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
     * Set origen
     *
     * @param \Ciudad $origen
     * @return Cronograma
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
     * @return Cronograma
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

    public function __toString()
    {
        return $this->nombre;
    }
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
        $this->estacionesPeajes = new \Doctrine\Common\Collections\ArrayCollection();        
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Cronograma
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add servicios
     *
     * @param \Servicio $servicios
     * @return Cronograma
     */
    public function addServicio(\Servicio $servicios)
    {
        $this->servicios[] = $servicios;

        return $this;
    }

    /**
     * Remove servicios
     *
     * @param \Servicio $servicios
     */
    public function removeServicio(\Servicio $servicios)
    {
        $this->servicios->removeElement($servicios);
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
     * @var integer
     */
    private $km;

    /**
     * @var \DateTime
     */
    private $tiempo_viaje;

    /**
     * @var float
     */
    private $precio_unitario;

    /**
     * @var \ClaseServicio
     */
    private $claseServicio;

    /**
     * @var \Cliente
     */
    private $clienteVacio;


    /**
     * Set km
     *
     * @param integer $km
     * @return Cronograma
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
     * Set tiempo_viaje
     *
     * @param \DateTime $tiempoViaje
     * @return Cronograma
     */
    public function setTiempoViaje($tiempoViaje)
    {
        $this->tiempo_viaje = $tiempoViaje;

        return $this;
    }

    /**
     * Get tiempo_viaje
     *
     * @return \DateTime 
     */
    public function getTiempoViaje()
    {
        return $this->tiempo_viaje;
    }

    /**
     * Set precio_unitario
     *
     * @param float $precioUnitario
     * @return Cronograma
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precio_unitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precio_unitario
     *
     * @return float 
     */
    public function getPrecioUnitario()
    {
        return $this->precio_unitario;
    }

    /**
     * Set claseServicio
     *
     * @param \ClaseServicio $claseServicio
     * @return Cronograma
     */
    public function setClaseServicio(\ClaseServicio $claseServicio)
    {
        $this->claseServicio = $claseServicio;

        return $this;
    }

    /**
     * Get claseServicio
     *
     * @return \ClaseServicio 
     */
    public function getClaseServicio()
    {
        return $this->claseServicio;
    }

    /**
     * Set clienteVacio
     *
     * @param \Cliente $clienteVacio
     * @return Cronograma
     */
    public function setClienteVacio(\Cliente $clienteVacio)
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifas;


    /**
     * Add tarifas
     *
     * @param \TarifaServicio $tarifas
     * @return Cronograma
     */
    public function addTarifa(\TarifaServicio $tarifas)
    {
        $this->tarifas[] = $tarifas;

        return $this;
    }

    /**
     * Remove tarifas
     *
     * @param \TarifaServicio $tarifas
     */
    public function removeTarifa(\TarifaServicio $tarifas)
    {
        $this->tarifas->removeElement($tarifas);
    }

    /**
     * Get tarifas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTarifas()
    {
        return $this->tarifas;
    }


    public function getArticuloFacturacion()
    {
        foreach ($this->tarifas as $tarifa) {  //recorre la coleccion de TarifaServicio
            $tfa = $tarifa->getTarifaPresupuestada();
            if ($tfa)
            {
                return $tfa->getArticulo();
            }
        }
        return null;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $estacionesPeajes;


    /**
     * Add estacionesPeajes
     *
     * @param \EstacionPeaje $estacionesPeajes
     * @return Cronograma
     */
    public function addEstacionesPeaje(\EstacionPeaje $estacionesPeajes)
    {
        $this->estacionesPeajes[] = $estacionesPeajes;

        return $this;
    }

    /**
     * Remove estacionesPeajes
     *
     * @param \EstacionPeaje $estacionesPeajes
     */
    public function removeEstacionesPeaje(\EstacionPeaje $estacionesPeajes)
    {
        $this->estacionesPeajes->removeElement($estacionesPeajes);
    }

    /**
     * Get estacionesPeajes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEstacionesPeajes()
    {
        return $this->estacionesPeajes;
    }
    /**
     * @var string
     */
    private $tipoServicio;


    /**
     * Set tipoServicio
     *
     * @param string $tipoServicio
     * @return Cronograma
     */
    public function setTipoServicio($tipoServicio)
    {
        $this->tipoServicio = $tipoServicio;

        return $this;
    }

    /**
     * Get tipoServicio
     *
     * @return string 
     */
    public function getTipoServicio()
    {
        return $this->tipoServicio;
    }
}
