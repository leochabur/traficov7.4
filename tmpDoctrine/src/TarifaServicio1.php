<?php



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
     * @var integer
     */
    private $id;

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
    /**
     * @var float
     */
    private $importe;


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
}
