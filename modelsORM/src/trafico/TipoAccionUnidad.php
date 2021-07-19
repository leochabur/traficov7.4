<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TipoAccionUnidad
 */
class TipoAccionUnidad
{
    /**
     * @var string
     */
    private $tipo;

    /**
     * @var boolean
     */
    private $prioritaria;

    /**
     * @var boolean
     */
    private $banio;

    /**
     * @var boolean
     */
    private $comenta;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set tipo
     *
     * @param string $tipo
     * @return TipoAccionUnidad
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set prioritaria
     *
     * @param boolean $prioritaria
     * @return TipoAccionUnidad
     */
    public function setPrioritaria($prioritaria)
    {
        $this->prioritaria = $prioritaria;

        return $this;
    }

    /**
     * Get prioritaria
     *
     * @return boolean 
     */
    public function getPrioritaria()
    {
        return $this->prioritaria;
    }

    /**
     * Set banio
     *
     * @param boolean $banio
     * @return TipoAccionUnidad
     */
    public function setBanio($banio)
    {
        $this->banio = $banio;

        return $this;
    }

    /**
     * Get banio
     *
     * @return boolean 
     */
    public function getBanio()
    {
        return $this->banio;
    }

    /**
     * Set comenta
     *
     * @param boolean $comenta
     * @return TipoAccionUnidad
     */
    public function setComenta($comenta)
    {
        $this->comenta = $comenta;

        return $this;
    }

    /**
     * Get comenta
     *
     * @return boolean 
     */
    public function getComenta()
    {
        return $this->comenta;
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
