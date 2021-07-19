<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ServicioInstancia
 */
class ServicioInstancia
{
    /**
     * @var string
     */
    private $instancia;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Servicio
     */
    private $servicio;


    /**
     * Set instancia
     *
     * @param string $instancia
     * @return ServicioInstancia
     */
    public function setInstancia($instancia)
    {
        $this->instancia = $instancia;

        return $this;
    }

    /**
     * Get instancia
     *
     * @return string 
     */
    public function getInstancia()
    {
        return $this->instancia;
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
     * Set servicio
     *
     * @param \Servicio $servicio
     * @return ServicioInstancia
     */
    public function setServicio(\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \Servicio 
     */
    public function getServicio()
    {
        return $this->servicio;
    }
}
