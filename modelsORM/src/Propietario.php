<?php



/**
 * Propietario
 */
class Propietario
{
    /**
     * @var string
     */
    private $razonSocial;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var integer
     */
    private $estructura;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     *
     * @return Propietario
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
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Propietario
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
     * Set estructura
     *
     * @param integer $estructura
     *
     * @return Propietario
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
     * @var string
     */
    private $direccion;

    /**
     * @var string
     */
    private $cuit;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var string
     */
    private $mail;

    /**
     * @var string
     */
    private $www;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $usr;

    /**
     * @var string
     */
    private $pwd;


    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Propietario
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
     * Set cuit
     *
     * @param string $cuit
     * @return Propietario
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
     * Set telefono
     *
     * @param string $telefono
     * @return Propietario
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
     * Set mail
     *
     * @param string $mail
     * @return Propietario
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set www
     *
     * @param string $www
     * @return Propietario
     */
    public function setWww($www)
    {
        $this->www = $www;

        return $this;
    }

    /**
     * Get www
     *
     * @return string 
     */
    public function getWww()
    {
        return $this->www;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Propietario
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set usr
     *
     * @param string $usr
     * @return Propietario
     */
    public function setUsr($usr)
    {
        $this->usr = $usr;

        return $this;
    }

    /**
     * Get usr
     *
     * @return string 
     */
    public function getUsr()
    {
        return $this->usr;
    }

    /**
     * Set pwd
     *
     * @param string $pwd
     * @return Propietario
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;

        return $this;
    }

    /**
     * Get pwd
     *
     * @return string 
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Propietario
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
