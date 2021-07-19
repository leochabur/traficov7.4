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
     * @var float
     */
    private $importe;

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
     * Set importe
     *
     * @param float $importe
     * @return ArticuloCliente
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return float 
     */
    public function getImporte()
    {
        return $this->importe;
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
    public function setCliente(\Cliente $cliente)
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
}
