<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ClaseCurso
 */
class ClaseCurso
{
    /**
     * @var string
     */
    private $codigo;

    /**
     * @var string
     */
    private $titulo;

    /**
     * @var float
     */
    private $orden;

    /**
     * @var string
     */
    private $recurso;

    /**
     * @var boolean
     */
    private $esEvaluacion;

    /**
     * @var boolean
     */
    private $eliminada;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineas;

    /**
     * @var \Curso
     */
    private $curso;

    /**
     * @var \ClaseCurso
     */
    private $anterior;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return ClaseCurso
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return ClaseCurso
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set orden
     *
     * @param float $orden
     * @return ClaseCurso
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return float 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set recurso
     *
     * @param string $recurso
     * @return ClaseCurso
     */
    public function setRecurso($recurso)
    {
        $this->recurso = $recurso;

        return $this;
    }

    /**
     * Get recurso
     *
     * @return string 
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * Set esEvaluacion
     *
     * @param boolean $esEvaluacion
     * @return ClaseCurso
     */
    public function setEsEvaluacion($esEvaluacion)
    {
        $this->esEvaluacion = $esEvaluacion;

        return $this;
    }

    /**
     * Get esEvaluacion
     *
     * @return boolean 
     */
    public function getEsEvaluacion()
    {
        return $this->esEvaluacion;
    }

    /**
     * Set eliminada
     *
     * @param boolean $eliminada
     * @return ClaseCurso
     */
    public function setEliminada($eliminada)
    {
        $this->eliminada = $eliminada;

        return $this;
    }

    /**
     * Get eliminada
     *
     * @return boolean 
     */
    public function getEliminada()
    {
        return $this->eliminada;
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
     * Add lineas
     *
     * @param \LineaDescripcionClase $lineas
     * @return ClaseCurso
     */
    public function addLinea(\LineaDescripcionClase $lineas)
    {
        $this->lineas[] = $lineas;

        return $this;
    }

    /**
     * Remove lineas
     *
     * @param \LineaDescripcionClase $lineas
     */
    public function removeLinea(\LineaDescripcionClase $lineas)
    {
        $this->lineas->removeElement($lineas);
    }

    /**
     * Get lineas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLineas()
    {
        return $this->lineas;
    }

    /**
     * Set curso
     *
     * @param \Curso $curso
     * @return ClaseCurso
     */
    public function setCurso(\Curso $curso = null)
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * Get curso
     *
     * @return \Curso 
     */
    public function getCurso()
    {
        return $this->curso;
    }

    /**
     * Set anterior
     *
     * @param \ClaseCurso $anterior
     * @return ClaseCurso
     */
    public function setAnterior(\ClaseCurso $anterior = null)
    {
        $this->anterior = $anterior;

        return $this;
    }

    /**
     * Get anterior
     *
     * @return \ClaseCurso 
     */
    public function getAnterior()
    {
        return $this->anterior;
    }
}
