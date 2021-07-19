<?php



/**
 * Empleado
 */
class Empleado
{
    /**
     * @var string
     */
    private $apellido;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var integer
     */
    private $legajo;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var boolean
     */
    private $borrado;

    /**
     * @var string
     */
    private $dni;

    /**
     * @var boolean
     */
    private $procesado;

    /**
     * @var integer
     */
    private $estructura;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Categoria
     */
    private $categoria;


    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Empleado
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    
        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Empleado
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set legajo
     *
     * @param integer $legajo
     *
     * @return Empleado
     */
    public function setLegajo($legajo)
    {
        $this->legajo = $legajo;
    
        return $this;
    }

    /**
     * Get legajo
     *
     * @return integer
     */
    public function getLegajo()
    {
        return $this->legajo;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Empleado
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set borrado
     *
     * @param boolean $borrado
     *
     * @return Empleado
     */
    public function setBorrado($borrado)
    {
        $this->borrado = $borrado;
    
        return $this;
    }

    /**
     * Get borrado
     *
     * @return boolean
     */
    public function getBorrado()
    {
        return $this->borrado;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return Empleado
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    
        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set procesado
     *
     * @param boolean $procesado
     *
     * @return Empleado
     */
    public function setProcesado($procesado)
    {
        $this->procesado = $procesado;
    
        return $this;
    }

    /**
     * Get procesado
     *
     * @return boolean
     */
    public function getProcesado()
    {
        return $this->procesado;
    }

    /**
     * Set estructura
     *
     * @param integer $estructura
     *
     * @return Empleado
     */
    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;
    
        return $this;
    }

    /**
     * Get estructura
     *
     * @return integer
     */
    public function getEstructura()
    {
        return $this->estructura;
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
     * Set categoria
     *
     * @param \Categoria $categoria
     *
     * @return Empleado
     */
    public function setCategoria(\Categoria $categoria = null)
    {
        $this->categoria = $categoria;
    
        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Categoria
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    public function __toString()
    {
        return $this->apellido.", ".$this->nombre;
    }
    /**
     * @var string
     */
    private $domicilio;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var integer
     */
    private $nacionalidad;

    /**
     * @var string
     */
    private $sexo;

    /**
     * @var \DateTime
     */
    private $fechaNac;

    /**
     * @var string
     */
    private $tipoDocumento;

    /**
     * @var string
     */
    private $numeroDocumento;

    /**
     * @var string
     */
    private $cuil;

    /**
     * @var \DateTime
     */
    private $fechaInicio;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var integer
     */
    private $nivelAcceso;

    /**
     * @var boolean
     */
    private $contratado;

    /**
     * @var \DateTime
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     */
    private $fechaOcupacional;

    /**
     * @var \DateTime
     */
    private $fechaAltaDefinitiva;

    /**
     * @var \DateTime
     */
    private $fechaFin;

    /**
     * @var \Ciudad
     */
    private $ciudad;

    /**
     * @var \Propietario
     */
    private $empleador;

    /**
     * @var \Estructura
     */
    private $estructuraAfectado;

    /**
     * @var \Usuario
     */
    private $usuarioAltaProvisoria;

    /**
     * @var \Usuario
     */
    private $usuarioAltaDefinitiva;


    /**
     * Set domicilio
     *
     * @param string $domicilio
     * @return Empleado
     */
    public function setDomicilio($domicilio)
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return string 
     */
    public function getDomicilio()
    {
        return $this->domicilio;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Empleado
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set nacionalidad
     *
     * @param integer $nacionalidad
     * @return Empleado
     */
    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    /**
     * Get nacionalidad
     *
     * @return integer 
     */
    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }

    /**
     * Set sexo
     *
     * @param string $sexo
     * @return Empleado
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set fechaNac
     *
     * @param \DateTime $fechaNac
     * @return Empleado
     */
    public function setFechaNac($fechaNac)
    {
        $this->fechaNac = $fechaNac;

        return $this;
    }

    /**
     * Get fechaNac
     *
     * @return \DateTime 
     */
    public function getFechaNac()
    {
        return $this->fechaNac;
    }

    /**
     * Set tipoDocumento
     *
     * @param string $tipoDocumento
     * @return Empleado
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return string 
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     * @return Empleado
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string 
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set cuil
     *
     * @param string $cuil
     * @return Empleado
     */
    public function setCuil($cuil)
    {
        $this->cuil = $cuil;

        return $this;
    }

    /**
     * Get cuil
     *
     * @return string 
     */
    public function getCuil()
    {
        return $this->cuil;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Empleado
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return Empleado
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Empleado
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nivelAcceso
     *
     * @param integer $nivelAcceso
     * @return Empleado
     */
    public function setNivelAcceso($nivelAcceso)
    {
        $this->nivelAcceso = $nivelAcceso;

        return $this;
    }

    /**
     * Get nivelAcceso
     *
     * @return integer 
     */
    public function getNivelAcceso()
    {
        return $this->nivelAcceso;
    }

    /**
     * Set contratado
     *
     * @param boolean $contratado
     * @return Empleado
     */
    public function setContratado($contratado)
    {
        $this->contratado = $contratado;

        return $this;
    }

    /**
     * Get contratado
     *
     * @return boolean 
     */
    public function getContratado()
    {
        return $this->contratado;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Empleado
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set fechaOcupacional
     *
     * @param \DateTime $fechaOcupacional
     * @return Empleado
     */
    public function setFechaOcupacional($fechaOcupacional)
    {
        $this->fechaOcupacional = $fechaOcupacional;

        return $this;
    }

    /**
     * Get fechaOcupacional
     *
     * @return \DateTime 
     */
    public function getFechaOcupacional()
    {
        return $this->fechaOcupacional;
    }

    /**
     * Set fechaAltaDefinitiva
     *
     * @param \DateTime $fechaAltaDefinitiva
     * @return Empleado
     */
    public function setFechaAltaDefinitiva($fechaAltaDefinitiva)
    {
        $this->fechaAltaDefinitiva = $fechaAltaDefinitiva;

        return $this;
    }

    /**
     * Get fechaAltaDefinitiva
     *
     * @return \DateTime 
     */
    public function getFechaAltaDefinitiva()
    {
        return $this->fechaAltaDefinitiva;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return Empleado
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set ciudad
     *
     * @param \Ciudad $ciudad
     * @return Empleado
     */
    public function setCiudad(\Ciudad $ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return \Ciudad 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set empleador
     *
     * @param \Propietario $empleador
     * @return Empleado
     */
    public function setEmpleador(\Propietario $empleador = null)
    {
        $this->empleador = $empleador;

        return $this;
    }

    /**
     * Get empleador
     *
     * @return \Propietario 
     */
    public function getEmpleador()
    {
        return $this->empleador;
    }

    /**
     * Set estructuraAfectado
     *
     * @param \Estructura $estructuraAfectado
     * @return Empleado
     */
    public function setEstructuraAfectado(\Estructura $estructuraAfectado = null)
    {
        $this->estructuraAfectado = $estructuraAfectado;

        return $this;
    }

    /**
     * Get estructuraAfectado
     *
     * @return \Estructura 
     */
    public function getEstructuraAfectado()
    {
        return $this->estructuraAfectado;
    }

    /**
     * Set usuarioAltaProvisoria
     *
     * @param \Usuario $usuarioAltaProvisoria
     * @return Empleado
     */
    public function setUsuarioAltaProvisoria(\Usuario $usuarioAltaProvisoria = null)
    {
        $this->usuarioAltaProvisoria = $usuarioAltaProvisoria;

        return $this;
    }

    /**
     * Get usuarioAltaProvisoria
     *
     * @return \Usuario 
     */
    public function getUsuarioAltaProvisoria()
    {
        return $this->usuarioAltaProvisoria;
    }

    /**
     * Set usuarioAltaDefinitiva
     *
     * @param \Usuario $usuarioAltaDefinitiva
     * @return Empleado
     */
    public function setUsuarioAltaDefinitiva(\Usuario $usuarioAltaDefinitiva = null)
    {
        $this->usuarioAltaDefinitiva = $usuarioAltaDefinitiva;

        return $this;
    }

    /**
     * Get usuarioAltaDefinitiva
     *
     * @return \Usuario 
     */
    public function getUsuarioAltaDefinitiva()
    {
        return $this->usuarioAltaDefinitiva;
    }
}
