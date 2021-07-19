<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Estructura
 */
class Estructura
{
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $direccion;

    /**
     * @var integer
     */
    private $tripulacion;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Estructura
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
