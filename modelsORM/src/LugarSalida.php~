<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * LugarSalida
 */
class LugarSalida
{
    /**
     * @var string
     */
    private $codigo;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \ServicioInstancia
     */
    private $primario;

    /**
     * @var \ServicioInstancia
     */
    private $secundario;


    /**
     * Set codigo
     *
     * @param string $codigo
     * @return LugarSalida
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set primario
     *
     * @param \ServicioInstancia $primario
     * @return LugarSalida
     */
    public function setPrimario(\ServicioInstancia $primario = null)
    {
        $this->primario = $primario;

        return $this;
    }

    /**
     * Get primario
     *
     * @return \ServicioInstancia 
     */
    public function getPrimario()
    {
        return $this->primario;
    }

    /**
     * Set secundario
     *
     * @param \ServicioInstancia $secundario
     * @return LugarSalida
     */
    public function setSecundario(\ServicioInstancia $secundario = null)
    {
        $this->secundario = $secundario;

        return $this;
    }

    /**
     * Get secundario
     *
     * @return \ServicioInstancia 
     */
    public function getSecundario()
    {
        return $this->secundario;
    }
}
