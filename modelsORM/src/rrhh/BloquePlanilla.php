<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * BloquePlanilla
 */
class BloquePlanilla
{
    /**
     * @var string
     */
    private $tituloEntrada;

    /**
     * @var string
     */
    private $tituloSalida;

    /**
     * @var string
     */
    private $tituloBloque;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineas;

    /**
     * @var \PlanillaDiaria
     */
    private $planilla;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set tituloEntrada
     *
     * @param string $tituloEntrada
     * @return BloquePlanilla
     */
    public function setTituloEntrada($tituloEntrada)
    {
        $this->tituloEntrada = $tituloEntrada;

        return $this;
    }

    /**
     * Get tituloEntrada
     *
     * @return string 
     */
    public function getTituloEntrada()
    {
        return $this->tituloEntrada;
    }

    /**
     * Set tituloSalida
     *
     * @param string $tituloSalida
     * @return BloquePlanilla
     */
    public function setTituloSalida($tituloSalida)
    {
        $this->tituloSalida = $tituloSalida;

        return $this;
    }

    /**
     * Get tituloSalida
     *
     * @return string 
     */
    public function getTituloSalida()
    {
        return $this->tituloSalida;
    }

    /**
     * Set tituloBloque
     *
     * @param string $tituloBloque
     * @return BloquePlanilla
     */
    public function setTituloBloque($tituloBloque)
    {
        $this->tituloBloque = $tituloBloque;

        return $this;
    }

    /**
     * Get tituloBloque
     *
     * @return string 
     */
    public function getTituloBloque()
    {
        return $this->tituloBloque;
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
     * @param \LineaPlanilla $lineas
     * @return BloquePlanilla
     */
    public function addLinea(\LineaPlanilla $lineas)
    {
        $this->lineas[] = $lineas;

        return $this;
    }

    /**
     * Remove lineas
     *
     * @param \LineaPlanilla $lineas
     */
    public function removeLinea(\LineaPlanilla $lineas)
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
     * Set planilla
     *
     * @param \PlanillaDiaria $planilla
     * @return BloquePlanilla
     */
    public function setPlanilla(\PlanillaDiaria $planilla = null)
    {
        $this->planilla = $planilla;

        return $this;
    }

    /**
     * Get planilla
     *
     * @return \PlanillaDiaria 
     */
    public function getPlanilla()
    {
        return $this->planilla;
    }
}
