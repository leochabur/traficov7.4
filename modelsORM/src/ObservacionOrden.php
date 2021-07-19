<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ObservacionOrden
 */
class ObservacionOrden
{
    /**
     * @var string
     */
    private $comentario;

    /**
     * @var \DateTime
     */
    private $fechaAccion;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Orden
     */
    private $orden;

    /**
     * @var \Usuario
     */
    private $usuario;

    /**
     * Set comentario
     *
     * @param string $comentario
     * @return ObservacionOrden
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string 
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set fechaAccion
     *
     * @param \DateTime $fechaAccion
     * @return ObservacionOrden
     */
    public function setFechaAccion($fechaAccion)
    {
        $this->fechaAccion = $fechaAccion;

        return $this;
    }

    /**
     * Get fechaAccion
     *
     * @return \DateTime 
     */
    public function getFechaAccion()
    {
        return $this->fechaAccion;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return ObservacionOrden
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
     * Set orden
     *
     * @param \Orden $orden
     * @return ObservacionOrden
     */
    public function setOrden(\Orden $orden = null)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return \Orden 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set usuario
     *
     * @param \Usuario $usuario
     * @return ObservacionOrden
     */
    public function setUsuario(\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    public function __toString()
    {
        return $this->comentario;
    }
}
