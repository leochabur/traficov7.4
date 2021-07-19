<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * PlanillaDiaria
 */
class PlanillaDiaria
{
    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $bloques;

    /**
     * @var \Cliente
     */
    private $cliente;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bloques = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return PlanillaDiaria
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
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
     * Add bloques
     *
     * @param \BloquePlanilla $bloques
     * @return PlanillaDiaria
     */
    public function addBloque(\BloquePlanilla $bloques)
    {
        $this->bloques[] = $bloques;

        return $this;
    }

    /**
     * Remove bloques
     *
     * @param \BloquePlanilla $bloques
     */
    public function removeBloque(\BloquePlanilla $bloques)
    {
        $this->bloques->removeElement($bloques);
    }

    /**
     * Get bloques
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBloques()
    {
        return $this->bloques;
    }

    /**
     * Set cliente
     *
     * @param \Cliente $cliente
     * @return PlanillaDiaria
     */
    public function setCliente(\Cliente $cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}
