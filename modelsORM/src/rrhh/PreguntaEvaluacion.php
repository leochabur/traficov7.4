<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * PreguntaEvaluacion
 */
class PreguntaEvaluacion
{
    /**
     * @var string
     */
    private $pregunta;

    /**
     * @var float
     */
    private $puntaje;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $respuestas;

    /**
     * @var \ClaseAulaVirtual
     */
    private $clase;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->respuestas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set pregunta
     *
     * @param string $pregunta
     * @return PreguntaEvaluacion
     */
    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    /**
     * Get pregunta
     *
     * @return string 
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }

    /**
     * Set puntaje
     *
     * @param float $puntaje
     * @return PreguntaEvaluacion
     */
    public function setPuntaje($puntaje)
    {
        $this->puntaje = $puntaje;

        return $this;
    }

    /**
     * Get puntaje
     *
     * @return float 
     */
    public function getPuntaje()
    {
        return $this->puntaje;
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
     * Add respuestas
     *
     * @param \RespuestaPregunta $respuestas
     * @return PreguntaEvaluacion
     */
    public function addRespuesta(\RespuestaPregunta $respuestas)
    {
        $this->respuestas[] = $respuestas;

        return $this;
    }

    /**
     * Remove respuestas
     *
     * @param \RespuestaPregunta $respuestas
     */
    public function removeRespuesta(\RespuestaPregunta $respuestas)
    {
        $this->respuestas->removeElement($respuestas);
    }

    /**
     * Get respuestas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRespuestas()
    {
        return $this->respuestas;
    }

    /**
     * Set clase
     *
     * @param \ClaseAulaVirtual $clase
     * @return PreguntaEvaluacion
     */
    public function setClase(\ClaseAulaVirtual $clase = null)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return \ClaseAulaVirtual 
     */
    public function getClase()
    {
        return $this->clase;
    }
}
