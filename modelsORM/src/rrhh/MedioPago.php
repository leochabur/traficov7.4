<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * MedioPago
 */
class MedioPago
{
    /**
     * @var string
     */
    private $medioPago;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set medioPago
     *
     * @param string $medioPago
     * @return MedioPago
     */
    public function setMedioPago($medioPago)
    {
        $this->medioPago = $medioPago;

        return $this;
    }

    /**
     * Get medioPago
     *
     * @return string 
     */
    public function getMedioPago()
    {
        return $this->medioPago;
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
