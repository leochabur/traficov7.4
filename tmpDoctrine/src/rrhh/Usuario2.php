<?php


/**
 * @author Leo
 * @version 1.0
 * @created 16-feb.-2018 3:35:31 p. m.
 */
class Usuario
{

	var $id;
	var $username;
	var $password;

	function Categoria()
	{
	}



	function getId()
	{
		return $this->id;
	}

	function getUsername()
	{
		return $this->username;
	}

	/**
	 * 
	 * @param newVal
	 */
	function setUsername($newVal)
	{
		$this->username = $newVal;
	}

}
