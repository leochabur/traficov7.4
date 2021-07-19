<?php
use Doctrine\ORM\Annotation as ORM;
/**
 * @Entity @Table(name="estructuras")
 */
class Estructura
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;
    
    /**
      * @Column(type="string")
    **/
    protected $nombre;


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
}
