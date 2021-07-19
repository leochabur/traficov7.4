<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Telefono
 */
class Telefono
{
    /**
     * @var integer
     */
    private $numero;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $usuario;

    /**
     * @var string
     */
    private $ubicacion;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var string
     */
    private $servicio;

    /**
     * @var string
     */
    private $ultimoModelo;

    /**
     * @var string
     */
    private $imei;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set numero
     *
     * @param integer $numero
     * @return Telefono
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Telefono
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return Telefono
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set ubicacion
     *
     * @param string $ubicacion
     * @return Telefono
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return string 
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Telefono
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
     * Set servicio
     *
     * @param string $servicio
     * @return Telefono
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return string 
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set ultimoModelo
     *
     * @param string $ultimoModelo
     * @return Telefono
     */
    public function setUltimoModelo($ultimoModelo)
    {
        $this->ultimoModelo = $ultimoModelo;

        return $this;
    }

    /**
     * Get ultimoModelo
     *
     * @return string 
     */
    public function getUltimoModelo()
    {
        return $this->ultimoModelo;
    }

    /**
     * Set imei
     *
     * @param string $imei
     * @return Telefono
     */
    public function setImei($imei)
    {
        $this->imei = $imei;

        return $this;
    }

    /**
     * Get imei
     *
     * @return string 
     */
    public function getImei()
    {
        return $this->imei;
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
     * @var \UbicacionTelefono
     */
    private $ubicacionTelefono;

    /**
     * @var \EstadoTelefono
     */
    private $estado;


    /**
     * Set ubicacionTelefono
     *
     * @param \UbicacionTelefono $ubicacionTelefono
     * @return Telefono
     */
    public function setUbicacionTelefono(\UbicacionTelefono $ubicacionTelefono = null)
    {
        $this->ubicacionTelefono = $ubicacionTelefono;

        return $this;
    }

    /**
     * Get ubicacionTelefono
     *
     * @return \UbicacionTelefono 
     */
    public function getUbicacionTelefono()
    {
        return $this->ubicacionTelefono;
    }

    /**
     * Set estado
     *
     * @param \EstadoTelefono $estado
     * @return Telefono
     */
    public function setEstado(\EstadoTelefono $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \EstadoTelefono 
     */
    public function getEstado()
    {
        return $this->estado;
    }
    /**
     * @var string
     */
    private $eliminado;


    /**
     * Set eliminado
     *
     * @param string $eliminado
     * @return Telefono
     */
    public function setEliminado($eliminado)
    {
        $this->eliminado = $eliminado;

        return $this;
    }

    /**
     * Get eliminado
     *
     * @return string 
     */
    public function getEliminado()
    {
        return $this->eliminado;
    }
}
