<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Curso
 */
class Curso
{
    /**
     * @var string
     */
    private $codigo;

    /**
     * @var string
     */
    private $nombre;
  
    /**
     * @var boolean
     */
    private $activo;

    /**
     * @var string
     */
    private $descripcion;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $clases;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $empleados;

    /**
     * Constructor
     */

    public function __toString()
    {
        return $this->nombre;
    }

    public function __construct()
    {
        $this->clases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->empleados = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return Curso
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Curso
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
     * Set activo
     *
     * @param boolean $activo
     * @return Curso
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Curso
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
     * Add clases
     *
     * @param \ClaseAulaVirtual $clases
     * @return Curso
     */
    public function addClase(\ClaseAulaVirtual $clases)
    {
        $this->clases[] = $clases;

        return $this;
    }

    public function getDetalleClases()
    {
        $info = array('cantClases' => 0, 'cantEval' => 0);

        foreach ($this->clases as $clase)
        {
            if (!$clase->getEliminada())
            {
                if ($clase->getEsEvaluacion())
                {
                    $info['cantEval']++;
                }
                else
                {
                    $info['cantClases']++;
                }
            }
        }
        return $info;
    }

    /**
     * Remove clases
     *
     * @param \ClaseAulaVirtual $clases
     */
    public function removeClase(\ClaseAulaVirtual $clases)
    {
        $this->clases->removeElement($clases);
    }

    /**
     * Get clases
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClases()
    {
        return $this->clases;
    }

    /**
     * Add empleados
     *
     * @param \Empleado $empleados
     * @return Curso
     */
    public function addEmpleado(\Empleado $empleados)
    {
        $this->empleados[] = $empleados;

        return $this;
    }

    /**
     * Remove empleados
     *
     * @param \Empleado $empleados
     */
    public function removeEmpleado(\Empleado $empleados)
    {
        $this->empleados->removeElement($empleados);
    }

    /**
     * Get empleados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmpleados()
    {
        return $this->empleados;
    }
    /**
     * @var boolean
     */
    private $admiteEvaluacion = 'false';


    /**
     * Set admiteEvaluacion
     *
     * @param boolean $admiteEvaluacion
     * @return Curso
     */
    public function setAdmiteEvaluacion($admiteEvaluacion)
    {
        $this->admiteEvaluacion = $admiteEvaluacion;

        return $this;
    }

    /**
     * Get admiteEvaluacion
     *
     * @return boolean 
     */
    public function getAdmiteEvaluacion()
    {
        return $this->admiteEvaluacion;
    }
}
