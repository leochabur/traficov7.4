<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * RespuestaPreguntaRealizada
 */
class RespuestaPreguntaRealizada
{
    /**
     * @var \DateTime
     */
    private $fecha;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \ClaseRealizada
     */
    private $claseRealizada;

    /**
     * @var \RespuestaPregunta
     */
    private $respuesta;


    public function __toString()
    {
        return $this->respuesta."";
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return RespuestaPreguntaRealizada
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set claseRealizada
     *
     * @param \ClaseRealizada $claseRealizada
     * @return RespuestaPreguntaRealizada
     */
    public function setClaseRealizada(\ClaseRealizada $claseRealizada = null)
    {
        $this->claseRealizada = $claseRealizada;

        return $this;
    }

    /**
     * Get claseRealizada
     *
     * @return \ClaseRealizada 
     */
    public function getClaseRealizada()
    {
        return $this->claseRealizada;
    }

    /**
     * Set respuesta
     *
     * @param \RespuestaPregunta $respuesta
     * @return RespuestaPreguntaRealizada
     */
    public function setRespuesta(\RespuestaPregunta $respuesta = null)
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    /**
     * Get respuesta
     *
     * @return \RespuestaPregunta 
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }
}
