<?php



/**
 * Cliente
 */
class Cliente
{
    /**
     * @var string
     */
    private $razonSocial;

    /**
     * @var string
     */
    private $cuit;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Estructura
     */
    private $estructura;


    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return Cliente
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;
    
        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     *
     * @return Cliente
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
    
        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Cliente
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
     *
     * @return Cliente
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

    public function __toString()
    {
        return ucwords(strtolower($this->razonSocial));
    }
    /**
     * @var string
     */
    private $telefono;

    /**
     * @var string
     */
    private $direccion;

    /**
     * @var \ResponsabilidadIVA
     */
    private $responsabilidad;


    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Cliente
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Cliente
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
     * Set id
     *
     * @param integer $id
     * @return Cliente
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set responsabilidad
     *
     * @param \ResponsabilidadIVA $responsabilidad
     * @return Cliente
     */
    public function setResponsabilidad(\ResponsabilidadIVA $responsabilidad = null)
    {
        $this->responsabilidad = $responsabilidad;

        return $this;
    }

    /**
     * Get responsabilidad
     *
     * @return \ResponsabilidadIVA 
     */
    public function getResponsabilidad()
    {
        return $this->responsabilidad;
    }
}
