<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TacografoUnidad
 */
class TacografoUnidad
{
    /**
     * @var \DateTime
     */
    private $vencimiento;

    /**
     * @var \DateTime
     */
    private $fechaCambio;

    /**
     * @var \DateTime
     */
    private $fechaAlta;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Unidad
     */
    private $unidad;

    /**
     * @var \Tacografo
     */
    private $tacografo;


    /**
     * Set vencimiento
     *
     * @param \DateTime $vencimiento
     * @return TacografoUnidad
     */
    public function setVencimiento($vencimiento)
    {
        $this->vencimiento = $vencimiento;

        return $this;
    }

    /**
     * Get vencimiento
     *
     * @return \DateTime 
     */
    public function getVencimiento()
    {
        return $this->vencimiento;
    }

    /**
     * Set fechaCambio
     *
     * @param \DateTime $fechaCambio
     * @return TacografoUnidad
     */
    public function setFechaCambio($fechaCambio)
    {
        $this->fechaCambio = $fechaCambio;

        return $this;
    }

    /**
     * Get fechaCambio
     *
     * @return \DateTime 
     */
    public function getFechaCambio()
    {
        return $this->fechaCambio;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return TacografoUnidad
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
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
     * Set unidad
     *
     * @param \Unidad $unidad
     * @return TacografoUnidad
     */
    public function setUnidad(\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Unidad 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set tacografo
     *
     * @param \Tacografo $tacografo
     * @return TacografoUnidad
     */
    public function setTacografo(\Tacografo $tacografo = null)
    {
        $this->tacografo = $tacografo;

        return $this;
    }

    /**
     * Get tacografo
     *
     * @return \Tacografo 
     */
    public function getTacografo()
    {
        return $this->tacografo;
    }
    /**
     * @ORM\PrePersist
     */
    public function actualizarHorario()
    {
        // Add your code here
    }
}
