<?php


/**
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:31 p. m.
 */
class Categoria
{

	var $id;
	var $categoria;

	function Categoria()
	{
	}



	function getId()
	{
		return $this->id;
	}

	function getCategoria()
	{
		return $this->categoria;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setCategoria($newVal)
	{
		$this->categoria = $newVal;
	}

}
?>