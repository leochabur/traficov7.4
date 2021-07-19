<?php
use Doctrine\ORM\Annotation as ORM;
/**
 * @Entity @Table(name="estructuras")
 */
class Estructura
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;
    
    /**
      * @Column(type="string")
    **/
    protected $nombre;


    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    /**
     * @var string
     */
    private $direccion;

    /**
     * @var integer
     */
    private $tripulacion;


    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Estructura
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
     * Set tripulacion
     *
     * @param integer $tripulacion
     *
     * @return Estructura
     */
    public function setTripulacion($tripulacion)
    {
        $this->tripulacion = $tripulacion;
    
        return $this;
    }

    /**
     * Get tripulacion
     *
     * @return integer
     */
    public function getTripulacion()
    {
        return $this->tripulacion;
    }

    public function __toString()
    {
        return $this->nombre;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tiposVehiculos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tiposVehiculos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tiposVehiculos
     *
     * @param \TipoVehiculo $tiposVehiculos
     * @return Estructura
     */
    public function addTiposVehiculo(\TipoVehiculo $tiposVehiculos)
    {
        $this->tiposVehiculos[] = $tiposVehiculos;

        return $this;
    }

    /**
     * Remove tiposVehiculos
     *
     * @param \TipoVehiculo $tiposVehiculos
     */
    public function removeTiposVehiculo(\TipoVehiculo $tiposVehiculos)
    {
        $this->tiposVehiculos->removeElement($tiposVehiculos);
    }

    /**
     * Get tiposVehiculos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTiposVehiculos()
    {
        return $this->tiposVehiculos;
    }
}
