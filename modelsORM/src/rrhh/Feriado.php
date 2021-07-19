<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Feriado
 */
class Feriado
{
    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var \DateTime
     */
    private $fechaAlta;

    /**
     * @var boolean
     */
    private $eliminado;

    /**
     * @var \DateTime
     */
    private $fechaBaja;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Usuario
     */
    private $usuarioAlta;

    /**
     * @var \Estructura
     */
    private $estructura;

    /**
     * @var \Usuario
     */
    private $usuarioBaja;


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Feriado
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Feriado
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Feriado
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
     * Set eliminado
     *
     * @param boolean $eliminado
     * @return Feriado
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
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return Feriado
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Feriado
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
     * Set usuarioAlta
     *
     * @param \Usuario $usuarioAlta
     * @return Feriado
     */
    public function setUsuarioAlta(\Usuario $usuarioAlta = null)
    {
        $this->usuarioAlta = $usuarioAlta;

        return $this;
    }

    /**
     * Get usuarioAlta
     *
     * @return \Usuario 
     */
    public function getUsuarioAlta()
    {
        return $this->usuarioAlta;
    }

    /**
     * Set estructura
     *
     * @param \Estructura $estructura
     * @return Feriado
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
     * Set usuarioBaja
     *
     * @param \Usuario $usuarioBaja
     * @return Feriado
     */
    public function setUsuarioBaja(\Usuario $usuarioBaja = null)
    {
        $this->usuarioBaja = $usuarioBaja;

        return $this;
    }

    /**
     * Get usuarioBaja
     *
     * @return \Usuario 
     */
    public function getUsuarioBaja()
    {
        return $this->usuarioBaja;
    }
}
