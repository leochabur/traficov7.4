<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Turno
 */
class Turno
{
    /**
     * @var string
     */
    private $turno;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set turno
     *
     * @param string $turno
     * @return Turno
     */
    public function setTurno($turno)
    {
        $this->turno = $turno;

        return $this;
    }

    /**
     * Get turno
     *
     * @return string 
     */
    public function getTurno()
    {
        return $this->turno;
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
