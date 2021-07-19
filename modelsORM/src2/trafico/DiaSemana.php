<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * DiaSemana
 */
class DiaSemana
{
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var integer
     */
    private $numero_dia;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return DiaSemana
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set numero_dia
     *
     * @param integer $numeroDia
     * @return DiaSemana
     */
    public function setNumeroDia($numeroDia)
    {
        $this->numero_dia = $numeroDia;

        return $this;
    }

    /**
     * Get numero_dia
     *
     * @return integer 
     */
    public function getNumeroDia()
    {
        return $this->numero_dia;
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
