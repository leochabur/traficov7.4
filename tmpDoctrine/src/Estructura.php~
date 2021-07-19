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
}
