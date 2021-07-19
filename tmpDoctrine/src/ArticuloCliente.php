<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ArticuloCliente
 */
class ArticuloCliente
{
    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Cliente
     */
    private $cliente;


    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return ArticuloCliente
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
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
     * Set cliente
     *
     * @param \Cliente $cliente
     * @return ArticuloCliente
     */
    public function setCliente(\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    public function __toString()
    {
        return $this->descripcion;
    }
}
