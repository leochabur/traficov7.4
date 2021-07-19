<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TarifaServicio
 */
class TarifaServicio
{
    /**
     * @var string
     */
    private $nombre;

    /**
     * @var float
     */
    private $importe;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifasTipoVehiculo;

    /**
     * @var \FacturacionCliente
     */
    private $facturacion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $diasSemana;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cronogramas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarifasTipoVehiculo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->diasSemana = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cronogramas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return TarifaServicio
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
     * Set importe
     *
     * @param float $importe
     * @return TarifaServicio
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
     * Add tarifasTipoVehiculo
     *
     * @param \TarifaTipoServicio $tarifasTipoVehiculo
     * @return TarifaServicio
     */
    public function addTarifasTipoVehiculo(\TarifaTipoServicio $tarifasTipoVehiculo)
    {
        $this->tarifasTipoVehiculo[] = $tarifasTipoVehiculo;

        return $this;
    }

    /**
     * Remove tarifasTipoVehiculo
     *
     * @param \TarifaTipoServicio $tarifasTipoVehiculo
     */
    public function removeTarifasTipoVehiculo(\TarifaTipoServicio $tarifasTipoVehiculo)
    {
        $this->tarifasTipoVehiculo->removeElement($tarifasTipoVehiculo);
    }

    /**
     * Get tarifasTipoVehiculo
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTarifasTipoVehiculo()
    {
        return $this->tarifasTipoVehiculo;
    }

    /**
     * Set facturacion
     *
     * @param \FacturacionCliente $facturacion
     * @return TarifaServicio
     */
    public function setFacturacion(\FacturacionCliente $facturacion = null)
    {
        $this->facturacion = $facturacion;

        return $this;
    }

    /**
     * Get facturacion
     *
     * @return \FacturacionCliente 
     */
    public function getFacturacion()
    {
        return $this->facturacion;
    }

    /**
     * Add diasSemana
     *
     * @param \DiaSemana $diasSemana
     * @return TarifaServicio
     */
    public function addDiasSemana(\DiaSemana $diasSemana)
    {
        $this->diasSemana[] = $diasSemana;

        return $this;
    }

    /**
     * Remove diasSemana
     *
     * @param \DiaSemana $diasSemana
     */
    public function removeDiasSemana(\DiaSemana $diasSemana)
    {
        $this->diasSemana->removeElement($diasSemana);
    }

    /**
     * Get diasSemana
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiasSemana()
    {
        return $this->diasSemana;
    }

    /**
     * Add cronogramas
     *
     * @param \Cronograma $cronogramas
     * @return TarifaServicio
     */
    public function addCronograma(\Cronograma $cronogramas)
    {
        $this->cronogramas[] = $cronogramas;

        return $this;
    }

    /**
     * Remove cronogramas
     *
     * @param \Cronograma $cronogramas
     */
    public function removeCronograma(\Cronograma $cronogramas)
    {
        $this->cronogramas->removeElement($cronogramas);
    }

    /**
     * Get cronogramas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCronogramas()
    {
        return $this->cronogramas;
    }

    public function existeDia($dia)
    {
        foreach ($this->diasSemana as $ds) {
            if ($ds->getId() == $dia->getId())
                return true;
        }
        return false;
    }

    public function existeCronograma($crono)
    {
        foreach ($this->cronogramas as $cr) {
            if ($cr->getId() == $crono->getId())
                return true;
        }
        return false;
    }    
}
