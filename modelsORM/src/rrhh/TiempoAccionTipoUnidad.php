<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TiempoAccionTipoUnidad
 */
class TiempoAccionTipoUnidad
{
    /**
     * @var \DateTime
     */
    private $tiempo;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \TipoVehiculo
     */
    private $tipo;

    /**
     * @var \TipoAccionUnidad
     */
    private $tipoAccion;


    /**
     * Set tiempo
     *
     * @param \DateTime $tiempo
     * @return TiempoAccionTipoUnidad
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    /**
     * Get tiempo
     *
     * @return \DateTime 
     */
    public function getTiempo()
    {
        return $this->tiempo;
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
     * Set tipo
     *
     * @param \TipoVehiculo $tipo
     * @return TiempoAccionTipoUnidad
     */
    public function setTipo(\TipoVehiculo $tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \TipoVehiculo 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tipoAccion
     *
     * @param \TipoAccionUnidad $tipoAccion
     * @return TiempoAccionTipoUnidad
     */
    public function setTipoAccion(\TipoAccionUnidad $tipoAccion = null)
    {
        $this->tipoAccion = $tipoAccion;

        return $this;
    }

    /**
     * Get tipoAccion
     *
     * @return \TipoAccionUnidad 
     */
    public function getTipoAccion()
    {
        return $this->tipoAccion;
    }
}
