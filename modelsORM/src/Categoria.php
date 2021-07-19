<?php



/**
 * Categoria
 */
class Categoria
{
    /**
     * @var string
     */
    private $categoria;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set categoria
     *
     * @param string $categoria
     *
     * @return Categoria
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return string
     */
    public function getCategoria()
    {
        return $this->categoria;
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
     * @var string
     */
    private $descripcion;

    /**
     * @var \Estructura
     */
    private $estructura;


    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Categoria
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
     * Set id
     *
     * @param integer $id
     * @return Categoria
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set estructura
     *
     * @param \Estructura $estructura
     * @return Categoria
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
