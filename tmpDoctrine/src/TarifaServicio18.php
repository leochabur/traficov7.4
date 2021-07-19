<?php
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;


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
    private $importe = 0;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tarifasTipoVehiculo;

    /**
     * @var \TipoServicio
     */
    private $tipo;

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
     *
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
     *
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
     *
     * @return TarifaServicio
     */
    public function addTarifasTipoVehiculo(\TarifaTipoServicio $tarifasTipoVehiculo)
    {
        $tarifasTipoVehiculo->setTarifaServicio($this);
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
     * Set tipo
     *
     * @param \TipoServicio $tipo
     *
     * @return TarifaServicio
     */
    public function setTipo(\TipoServicio $tipo = null)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return \TipoServicio
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set facturacion
     *
     * @param \FacturacionCliente $facturacion
     *
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
     *
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
     * Add cronograma
     *
     * @param \Cronograma $cronograma
     *
     * @return TarifaServicio
     */
    public function addCronograma(\Cronograma $cronograma)
    {

        $this->cronogramas[] = $cronograma;
    
        return $this;
    }

    /**
     * Remove cronograma
     *
     * @param \Cronograma $cronograma
     */
    public function removeCronograma(\Cronograma $cronograma)
    {
        $this->cronogramas->removeElement($cronograma);
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

    public function __toString()
    {
        return $this->nombre;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('diasSemana', new Assert\Count(array(
            'min'        => 1,
            'minMessage' => 'Debe cargar la tarifa al menos para un dia de la semana!!'
        )));
    } 

    public function getTarifaTipoVehiculo($tipo){
     //   return $tipo;
        $tarifa;
        $exists = false;
        $valor;
        $default;
        $min = 999;
        $valueMin;
        foreach ($this->tarifasTipoVehiculo as $value) {
            if ($value->getTipo()->getId() == $tipo->getId()){
                $valor = $value;
                $exists = true;
            }
            elseif ($value->getDefecto()){
                $default = $value;
            }
            elseif ($min > $value->getTipo()->getOrden()){
                $min = $value->getTipo()->getOrden();
                $valueMin = $value;
            }
        }

        if ($exists)
            return $valor;
        else
            if ($default)
                return $default;
            else
                if ($min != 999)
                    return $valueMin;
                else
                    return "NO ENCUENTRA UN CARAJO $tipo - ".$this->nombre;
    } 

    public function existeTarifa($tarifaTipo)
    {
        foreach ($this->tarifasTipoVehiculo as $tarifa) {
            if ($tarifa->getTipo()->getId() == $tarifaTipo->getId()){
                return true;
            }
        }
        return false;
    }
}
