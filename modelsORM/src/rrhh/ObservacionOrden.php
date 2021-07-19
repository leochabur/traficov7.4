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
}
