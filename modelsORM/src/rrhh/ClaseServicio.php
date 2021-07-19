<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ClaseServicio
 */
class ClaseServicio
{
    /**
     * @var string
     */
    private $clase;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Estructura
     */
    private $estructura;


    /**
     * Set clase
     *
     * @param string $clase
     * @return ClaseServicio
     */
    public function setClase($clase)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return string 
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return ClaseServicio
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set estructura
     *
     * @param \Estructura $estructura
     * @return ClaseServicio
     */
    public function setEstructura(\Estructura $estructura)
    {
        $this->estructura = $estructura;

        return $this;
    }

    /**
     * Get estructura
     *
     * @return \Estructura 
     */
    public function getEstructura()
    {
        return $this->estructura;
    }
}
