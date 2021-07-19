<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Habilitacion
 */
class Habilitacion
{
    /**
     * @var \DateTime
     */
    private $desde;

    /**
     * @var \DateTime
     */
    private $hasta;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set desde
     *
     * @param \DateTime $desde
     * @return Habilitacion
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;

        return $this;
    }

    /**
     * Get desde
     *
     * @return \DateTime 
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     * @return Habilitacion
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;

        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime 
     */
    public function getHasta()
    {
        return $this->hasta;
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
