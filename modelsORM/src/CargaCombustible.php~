<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * CargaCombustible
 */
class CargaCombustible
{
    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var \DateTime
     */
    private $fechaAlta;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Turno
     */
    private $turno;

    /**
     * @var \Unidad
     */
    private $unidad;


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return CargaCombustible
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return CargaCombustible
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
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
     * Set turno
     *
     * @param \Turno $turno
     * @return CargaCombustible
     */
    public function setTurno(\Turno $turno = null)
    {
        $this->turno = $turno;

        return $this;
    }

    /**
     * Get turno
     *
     * @return \Turno 
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * Set unidad
     *
     * @param \Unidad $unidad
     * @return CargaCombustible
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


    public function actualizarHorario()
    {
        $this->fechaAlta = new \DateTime();
    }
    /**
     * @var integer
     */
    private $odometro;

    /**
     * @var float
     */
    private $litros;


    /**
     * Set odometro
     *
     * @param integer $odometro
     * @return CargaCombustible
     */
    public function setOdometro($odometro)
    {
        $this->odometro = $odometro;

        return $this;
    }

    /**
     * Get odometro
     *
     * @return integer 
     */
    public function getOdometro()
    {
        return $this->odometro;
    }

    /**
     * Set litros
     *
     * @param float $litros
     * @return CargaCombustible
     */
    public function setLitros($litros)
    {
        $this->litros = $litros;

        return $this;
    }

    /**
     * Get litros
     *
     * @return float 
     */
    public function getLitros()
    {
        return $this->litros;
    }
    /**
     * @var \Usuario
     */
    private $usuario;


    /**
     * Set usuario
     *
     * @param \Usuario $usuario
     * @return CargaCombustible
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
     * @var boolean
     */
    private $ingreso = false;


    /**
     * Set ingreso
     *
     * @param boolean $ingreso
     * @return CargaCombustible
     */
    public function setIngreso($ingreso)
    {
        $this->ingreso = $ingreso;

        return $this;
    }

    /**
     * Get ingreso
     *
     * @return boolean 
     */
    public function getIngreso()
    {
        return $this->ingreso;
    }
    /**
     * @var \Destino
     */    
    private $destino;


    /**
     * Set destino
     *
     * @param \Destino $destino
     * @return CargaCombustible
     */
    public function setDestino(\Destino $destino = null)
    {
        $this->destino = $destino;

        return $this;
    }

    /**
     * Get destino
     *
     * @return \Destino 
     */
    public function getDestino()
    {
        return $this->destino;
    }

    public function getAccion()
    {
        if (isset($this->unidad))
            return "Consumo";
        else
            return "Ingreso";
    }
    /**
     * @var string
     */
    private $proveedor;

    /**
     * @var string
     */
    private $factura;


    /**
     * Set proveedor
     *
     * @param string $proveedor
     * @return CargaCombustible
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return string 
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set factura
     *
     * @param string $factura
     * @return CargaCombustible
     */
    public function setFactura($factura)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return string 
     */
    public function getFactura()
    {
        return $this->factura;
    }
    /**
     * @var string
     */
    private $concepto;


    /**
     * Set concepto
     *
     * @param string $concepto
     * @return CargaCombustible
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string 
     */
    public function getConcepto()
    {
        return $this->concepto;
    }
    /**
     * @var \TipoFluido
     */
    private $tipoFluido;


    /**
     * Set tipoFluido
     *
     * @param \TipoFluido $tipoFluido
     * @return CargaCombustible
     */
    public function setTipoFluido(\TipoFluido $tipoFluido = null)
    {
        $this->tipoFluido = $tipoFluido;

        return $this;
    }

    /**
     * Get tipoFluido
     *
     * @return \TipoFluido 
     */
    public function getTipoFluido()
    {
        return $this->tipoFluido;
    }
    /**
     * @var \MotivoTAGMaestro
     */
    private $motivotagmaestro;


    /**
     * Set motivotagmaestro
     *
     * @param \MotivoTAGMaestro $motivotagmaestro
     * @return CargaCombustible
     */
    public function setMotivotagmaestro(\MotivoTAGMaestro $motivotagmaestro = null)
    {
        $this->motivotagmaestro = $motivotagmaestro;

        return $this;
    }

    /**
     * Get motivotagmaestro
     *
     * @return \MotivoTAGMaestro 
     */
    public function getMotivotagmaestro()
    {
        return $this->motivotagmaestro;
    }
    /**
     * @var boolean
     */
    private $usoTagMaestro = false;

    /**
     * @var string
     */
    private $descripcionMotivo;


    /**
     * Set usoTagMaestro
     *
     * @param boolean $usoTagMaestro
     * @return CargaCombustible
     */
    public function setUsoTagMaestro($usoTagMaestro)
    {
        $this->usoTagMaestro = $usoTagMaestro;

        return $this;
    }

    /**
     * Get usoTagMaestro
     *
     * @return boolean 
     */
    public function getUsoTagMaestro()
    {
        return $this->usoTagMaestro;
    }

    /**
     * Set descripcionMotivo
     *
     * @param string $descripcionMotivo
     * @return CargaCombustible
     */
    public function setDescripcionMotivo($descripcionMotivo)
    {
        $this->descripcionMotivo = $descripcionMotivo;

        return $this;
    }

    /**
     * Get descripcionMotivo
     *
     * @return string 
     */
    public function getDescripcionMotivo()
    {
        return $this->descripcionMotivo;
    }
}
