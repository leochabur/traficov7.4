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
}
