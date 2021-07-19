<?php
/*use Doctrine\ORM\Annotation as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;*/

class GrupoNovedad
{

    protected $id;
    
    protected $nombre;
    
    /**
     * @ManyToMany(targetEntity="NovedadTitulo")
     * @JoinTable(name="anomxgrupoinforme",
     *      joinColumns={@JoinColumn(name="id_grupo_informe", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="id_cod_anomalia", referencedColumnName="id")}
     *      )
     */
    protected $novedades;
    
    
    public function __construct() {
        $this->codigosNovedades = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    
    public function getNovedades()
    {
        return $this->novedades;
    }

    public function setNovedades($novedades)
    {
        $this->novedades = $novedades;
    }
    
    public function addNovedad($novedad)
    {
        $this->novedades[] = $novedad;
    }
    
    public function removeNovedad(NovedadTitulo $nov)
    {
           if (!$this->novedades->contains($nov)) {
              return;
           }
           $this->novedades->removeElement($nov);
    }
    
  /*  public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
           $metadata->addPropertyConstraint('nombre', new Assert\NotBlank());
        //   $metadata->addConstraint(new UniqueEntity(array('fields'  => 'nombre', 'message'   => 'This port is already in use on that host.',)));
    }*/
}
