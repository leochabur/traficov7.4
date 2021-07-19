<?php
	include ('bootstrap.php');



	function call($class, $function)
	{
		global $entityManager;
		$repository = $entityManager->getRepository($class);
    	$datas = $repository->$function();
   		return ($datas);	

	}
	
	function find($class, $id)
	{
		global $entityManager;
		$data = $entityManager->find($class, $id);
   		return ($data);
	}


?>
	
