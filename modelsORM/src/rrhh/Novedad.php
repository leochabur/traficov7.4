<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Novedad
 */
class Novedad
{
    /**
     * @var \DateTime
     */
    private $desde;

    /**
     * @var \DateTime
     */
    private $hasta;

    /**
     * @var string
     */
    private $estado;

    /**
     * @var boolean
     */
    private $activa;

    /**
     * @var boolean
     */
    private $pendiente;

    /**
     * @var \DateTime
     */
    private $fechaAlta;

    /**
     * @var string
     */
    private $usertxt;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Estructura
     */
    private $estructura;

    /**
     * @var \Empleado
     */
    private $empleado;

    /**
     * @var \NovedadTexto
     */
    private $novedadTexto;

    /**
     * @var \Usuario
     */
    private $usuario;


    /**
     * Set desde
     *
     * @param \DateTime $desde
     * @return Novedad
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;

        return $this;
    }

    /**
     * Get desde
     *
     * @return \DateTime 
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     * @return Novedad
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;

        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime 
     */
    public function getHasta()
    {
        return $this->hasta;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return Novedad
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set activa
     *
     * @param boolean $activa
     * @return Novedad
     */
    public function setActiva($activa)
    {
        $this->activa = $activa;

        return $this;
    }

    /**
     * Get activa
     *
     * @return boolean 
     */
    public function getActiva()
    {
        return $this->activa;
    }

    /**
     * Set pendiente
     *
     * @param boolean $pendiente
     * @return Novedad
     */
    public function setPendiente($pendiente)
    {
        $this->pendiente = $pendiente;

        return $this;
    }

    /**
     * Get pendiente
     *
     * @return boolean 
     */
    public function getPendiente()
    {
        return $this->pendiente;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Novedad
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
     * Set usertxt
     *
     * @param string $usertxt
     * @return Novedad
     */
    public function setUsertxt($usertxt)
    {
        $this->usertxt = $usertxt;

        return $this;
    }

    /**
     * Get usertxt
     *
     * @return string 
     */
    public function getUsertxt()
    {
        return $this->usertxt;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Novedad
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
     * Set estructura
     *
     * @param \Estructura $estructura
     * @return Novedad
     */
    public function setEstructura(\Estructura $estructura)
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
     * Set empleado
     *
     * @param \Empleado $empleado
     * @return Novedad
     */
    public function setEmpleado(\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \Empleado 
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }

    /**
     * Set novedadTexto
     *
     * @param \NovedadTexto $novedadTexto
     * @return Novedad
     */
    public function setNovedadTexto(\NovedadTexto $novedadTexto = null)
    {
        $this->novedadTexto = $novedadTexto;

        return $this;
    }

    /**
     * Get novedadTexto
     *
     * @return \NovedadTexto 
     */
    public function getNovedadTexto()
    {
        return $this->novedadTexto;
    }

    /**
     * Set usuario
     *
     * @param \Usuario $usuario
     * @return Novedad
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
