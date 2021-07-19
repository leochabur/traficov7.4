<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Usuario
 */
class Usuario
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var integer
     */
    private $nivel;

    /**
     * @var string
     */
    private $username;

    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var string
     */
    private $ultimoPass;

    /**
     * @var \DateTime
     */
    private $vencimiento;

    /**
     * @var string
     */
    private $puesto;

    /**
     * @var string
     */
    private $mail;

    /**
     * @var string
     */
    private $passwordPr;

    /**
     * @var \DateTime
     */
    private $fechaActualizacionPassword;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set user
     *
     * @param string $user
     * @return Usuario
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
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
     * Set nivel
     *
     * @param integer $nivel
     * @return Usuario
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Usuario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Usuario
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
     * Set ultimoPass
     *
     * @param string $ultimoPass
     * @return Usuario
     */
    public function setUltimoPass($ultimoPass)
    {
        $this->ultimoPass = $ultimoPass;

        return $this;
    }

    /**
     * Get ultimoPass
     *
     * @return string 
     */
    public function getUltimoPass()
    {
        return $this->ultimoPass;
    }

    /**
     * Set vencimiento
     *
     * @param \DateTime $vencimiento
     * @return Usuario
     */
    public function setVencimiento($vencimiento)
    {
        $this->vencimiento = $vencimiento;

        return $this;
    }

    /**
     * Get vencimiento
     *
     * @return \DateTime 
     */
    public function getVencimiento()
    {
        return $this->vencimiento;
    }

    /**
     * Set puesto
     *
     * @param string $puesto
     * @return Usuario
     */
    public function setPuesto($puesto)
    {
        $this->puesto = $puesto;

        return $this;
    }

    /**
     * Get puesto
     *
     * @return string 
     */
    public function getPuesto()
    {
        return $this->puesto;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Usuario
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set passwordPr
     *
     * @param string $passwordPr
     * @return Usuario
     */
    public function setPasswordPr($passwordPr)
    {
        $this->passwordPr = $passwordPr;

        return $this;
    }

    /**
     * Get passwordPr
     *
     * @return string 
     */
    public function getPasswordPr()
    {
        return $this->passwordPr;
    }

    /**
     * Set fechaActualizacionPassword
     *
     * @param \DateTime $fechaActualizacionPassword
     * @return Usuario
     */
    public function setFechaActualizacionPassword($fechaActualizacionPassword)
    {
        $this->fechaActualizacionPassword = $fechaActualizacionPassword;

        return $this;
    }

    /**
     * Get fechaActualizacionPassword
     *
     * @return \DateTime 
     */
    public function getFechaActualizacionPassword()
    {
        return $this->fechaActualizacionPassword;
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
}
