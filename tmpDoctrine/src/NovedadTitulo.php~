<?php
//use Doctrine\ORM\Annotation as ORM;
/**
 * @Entity @Table(name="cod_novedades")
 */
class NovedadTitulo
{    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;
     /**
     * @Column(type="string", name="nov_text")
     **/
    protected $descripcion;
    
    /**
     * @ManyToOne(targetEntity="Estructura")
     * @JoinColumn(name="id_estructura", referencedColumnName="id")
     **/
    protected $estructura;
    

    public function getId()
    {
        return $this->id;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($interno)
    {
        $this->descripcion = $interno;
    }
    
    public function getEstructura(){
           $this->estructura;
    }
    
    public function setEstructura($estructura){
           $this->estructura = $estructura;
    }
}
