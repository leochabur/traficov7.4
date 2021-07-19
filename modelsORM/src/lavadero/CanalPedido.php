<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * CanalPedido
 */
class CanalPedido
{
    /**
     * @var string
     */
    private $canal;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set canal
     *
     * @param string $canal
     * @return CanalPedido
     */
    public function setCanal($canal)
    {
        $this->canal = $canal;

        return $this;
    }

    /**
     * Get canal
     *
     * @return string 
     */
    public function getCanal()
    {
        return $this->canal;
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
