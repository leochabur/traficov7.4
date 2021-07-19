<?php

class GastoPresupuesto{

	private $id;

	private $nombre;


	public function getId()
	{
		return $this->id;
	}



    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }


    public function getNombre()
    {
        return $this->nombre;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}
