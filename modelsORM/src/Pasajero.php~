<?php


use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pasajero
 */
class Pasajero
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
     * @var string
     */
    private $direccion;

    /**
     * @var string
     */
    private $ciudad;

    /**
     * @var integer
     */
    private $dni;

    /**
     * @var float
     */
    private $latitud;

    /**
     * @var float
     */
    private $longtud;

    /**
     * @var boolean
     */
    private $activo = true;

    /**
     * @var integer
     */
    private $id;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('apellido', new Assert\NotBlank());
        $metadata->addPropertyConstraint('nombre', new Assert\NotBlank());
        $metadata->addPropertyConstraint('direccion', new Assert\NotBlank());
        $metadata->addPropertyConstraint('ciudad', new Assert\NotBlank());


        $metadata->addPropertyConstraint('dni', new Assert\Type(array(
            'type'    => 'integer',
            'message' => 'El dni debe ser de un numero',
        )));

        $metadata->addPropertyConstraint('latitud', new Assert\Type(array(
            'type'    => 'float',
            'message' => 'La latitud ser de un numero',
        )));

        $metadata->addPropertyConstraint('longtud', new Assert\Type(array(
            'type'    => 'float',
            'message' => 'La longitud debe ser de un numero',
        )));
    } 


    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Pasajero
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
     * @return Pasajero
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
     * Set direccion
     *
     * @param string $direccion
     * @return Pasajero
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
     * Set ciudad
     *
     * @param string $ciudad
     * @return Pasajero
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    
        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set dni
     *
     * @param integer $dni
     * @return Pasajero
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    
        return $this;
    }

    /**
     * Get dni
     *
     * @return integer 
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set latitud
     *
     * @param float $latitud
     * @return Pasajero
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;
    
        return $this;
    }

    /**
     * Get latitud
     *
     * @return float 
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longtud
     *
     * @param float $longtud
     * @return Pasajero
     */
    public function setLongtud($longtud)
    {
        $this->longtud = $longtud;
    
        return $this;
    }

    /**
     * Get longtud
     *
     * @return float 
     */
    public function getLongtud()
    {
        return $this->longtud;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Pasajero
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
}
