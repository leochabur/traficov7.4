<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * RespuestaPregunta
 */
class RespuestaPregunta
{
    /**
     * @var string
     */
    private $respuesta;

    /**
     * @var boolean
     */
    private $correcta = 'false';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \PreguntaEvaluacion
     */
    private $pregunta;


    /**
     * Set respuesta
     *
     * @param string $respuesta
     * @return RespuestaPregunta
     */
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    /**
     * Get respuesta
     *
     * @return string 
     */
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    /**
     * Set correcta
     *
     * @param boolean $correcta
     * @return RespuestaPregunta
     */
    public function setCorrecta($correcta)
    {
        $this->correcta = $correcta;

        return $this;
    }

    /**
     * Get correcta
     *
     * @return boolean 
     */
    public function getCorrecta()
    {
        return $this->correcta;
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
     * Set pregunta
     *
     * @param \PreguntaEvaluacion $pregunta
     * @return RespuestaPregunta
     */
    public function setPregunta(\PreguntaEvaluacion $pregunta = null)
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    /**
     * Get pregunta
     *
     * @return \PreguntaEvaluacion 
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }
}
