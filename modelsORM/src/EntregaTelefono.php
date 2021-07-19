<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * EntregaTelefono
 */
class EntregaTelefono
{
    /**
     * @var \DateTime
     */
    private $fechaEntrega;

    /**
     * @var \DateTime
     */
    private $fechaDevolucion;

    /**
     * @var \DateTime
     */
    private $fechaHoraEntrega;

    /**
     * @var \DateTime
     */
    private $fechaHoraDevolucion;

    /**
     * @var boolean
     */
    private $devuelto;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Usuario
     */
    private $usuarioEntrega;

    /**
     * @var \Usuario
     */
    private $usuarioDevolucion;

    /**
     * @var \Telefono
     */
    private $telefono;

    /**
     * @var \Empleado
     */
    private $empleado;


    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     * @return EntregaTelefono
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime 
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set fechaDevolucion
     *
     * @param \DateTime $fechaDevolucion
     * @return EntregaTelefono
     */
    public function setFechaDevolucion($fechaDevolucion)
    {
        $this->fechaDevolucion = $fechaDevolucion;

        return $this;
    }

    /**
     * Get fechaDevolucion
     *
     * @return \DateTime 
     */
    public function getFechaDevolucion()
    {
        return $this->fechaDevolucion;
    }

    /**
     * Set fechaHoraEntrega
     *
     * @param \DateTime $fechaHoraEntrega
     * @return EntregaTelefono
     */
    public function setFechaHoraEntrega($fechaHoraEntrega)
    {
        $this->fechaHoraEntrega = $fechaHoraEntrega;

        return $this;
    }

    /**
     * Get fechaHoraEntrega
     *
     * @return \DateTime 
     */
    public function getFechaHoraEntrega()
    {
        return $this->fechaHoraEntrega;
    }

    /**
     * Set fechaHoraDevolucion
     *
     * @param \DateTime $fechaHoraDevolucion
     * @return EntregaTelefono
     */
    public function setFechaHoraDevolucion($fechaHoraDevolucion)
    {
        $this->fechaHoraDevolucion = $fechaHoraDevolucion;

        return $this;
    }

    /**
     * Get fechaHoraDevolucion
     *
     * @return \DateTime 
     */
    public function getFechaHoraDevolucion()
    {
        return $this->fechaHoraDevolucion;
    }

    /**
     * Set devuelto
     *
     * @param boolean $devuelto
     * @return EntregaTelefono
     */
    public function setDevuelto($devuelto)
    {
        $this->devuelto = $devuelto;

        return $this;
    }

    /**
     * Get devuelto
     *
     * @return boolean 
     */
    public function getDevuelto()
    {
        return $this->devuelto;
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
     * Set usuarioEntrega
     *
     * @param \Usuario $usuarioEntrega
     * @return EntregaTelefono
     */
    public function setUsuarioEntrega(\Usuario $usuarioEntrega = null)
    {
        $this->usuarioEntrega = $usuarioEntrega;

        return $this;
    }

    /**
     * Get usuarioEntrega
     *
     * @return \Usuario 
     */
    public function getUsuarioEntrega()
    {
        return $this->usuarioEntrega;
    }

    /**
     * Set usuarioDevolucion
     *
     * @param \Usuario $usuarioDevolucion
     * @return EntregaTelefono
     */
    public function setUsuarioDevolucion(\Usuario $usuarioDevolucion = null)
    {
        $this->usuarioDevolucion = $usuarioDevolucion;

        return $this;
    }

    /**
     * Get usuarioDevolucion
     *
     * @return \Usuario 
     */
    public function getUsuarioDevolucion()
    {
        return $this->usuarioDevolucion;
    }

    /**
     * Set telefono
     *
     * @param \Telefono $telefono
     * @return EntregaTelefono
     */
    public function setTelefono(\Telefono $telefono = null)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return \Telefono 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set empleado
     *
     * @param \Empleado $empleado
     * @return EntregaTelefono
     */
    public function setEmpleado(\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \Empleado 
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }
}
