<?php
require_once ('Categoria.php');

/**
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:30 p. m.
 */
class Empleado
{

	protected $id;
	protected $apellido;
	protected $nombre;
	protected $legajo;
	protected $categoria;
	protected $activo;
	protected $dni;
	protected $procesado;
	protected $empleador;
	protected $estructura;	
	protected $borrado;	

	public function Empleado()
	{
	}



	public function getId()
	{
		return $this->id;
	}

	public function getApellido()
	{
		return $this->apellido;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setApellido($newVal)
	{
		$this->apellido = $newVal;
	}

	public function getNombre()
	{
		return $this->nombre;
	}

	public function getActivo()
	{
		return $this->activo;
	}

	public function getDni()
	{
		return $this->dni;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setNombre($newVal)
	{
		$this->nombre = $newVal;
	}

	public function getLegajo()
	{
		return $this->legajo;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setLegajo($newVal)
	{
		$this->legajo = $newVal;
	}

	public function getCategoria()
	{
		return $this->categoria;
	}

	/**
	 * 
	 * @param newVal
	 */
	public function setCategoria($newVal)
	{
		$this->categoria = $newVal;
	}

	public function setActivo($newVal)
	{
		$this->activo = $newVal;
	}

	public function __toString()
	{
		return strtoupper($this->apellido.', '.$this->nombre);
	}

}
?>