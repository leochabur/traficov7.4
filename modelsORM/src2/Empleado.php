<?php



/**
 * Empleado
 */
class Empleado
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
     * @var integer
     */
    private $legajo;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var boolean
     */
    private $borrado;

    /**
     * @var string
     */
    private $dni;

    /**
     * @var boolean
     */
    private $procesado;

    /**
     * @var integer
     */
    private $estructura;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Categoria
     */
    private $categoria;


    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Empleado
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
     *
     * @return Empleado
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
     * Set legajo
     *
     * @param integer $legajo
     *
     * @return Empleado
     */
    public function setLegajo($legajo)
    {
        $this->legajo = $legajo;
    
        return $this;
    }

    /**
     * Get legajo
     *
     * @return integer
     */
    public function getLegajo()
    {
        return $this->legajo;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Empleado
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
     * Set borrado
     *
     * @param boolean $borrado
     *
     * @return Empleado
     */
    public function setBorrado($borrado)
    {
        $this->borrado = $borrado;
    
        return $this;
    }

    /**
     * Get borrado
     *
     * @return boolean
     */
    public function getBorrado()
    {
        return $this->borrado;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return Empleado
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    
        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set procesado
     *
     * @param boolean $procesado
     *
     * @return Empleado
     */
    public function setProcesado($procesado)
    {
        $this->procesado = $procesado;
    
        return $this;
    }

    /**
     * Get procesado
     *
     * @return boolean
     */
    public function getProcesado()
    {
        return $this->procesado;
    }

    /**
     * Set estructura
     *
     * @param integer $estructura
     *
     * @return Empleado
     */
    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;
    
        return $this;
    }

    /**
     * Get estructura
     *
     * @return integer
     */
    public function getEstructura()
    {
        return $this->estructura;
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
     * Set categoria
     *
     * @param \Categoria $categoria
     *
     * @return Empleado
     */
    public function setCategoria(\Categoria $categoria = null)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    public function __toString()
    {
        return $this->apellido.", ".$this->nombre;
    }
}
