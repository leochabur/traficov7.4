<?php


//  Clase Base de datos 
class BdConexion {
	 
	private $con;
	private $error;
	private $stmt;
	private $host      = BD_HOST;
	private $user      = BD_USER;
	private $pass      = BD_PASS;
	private $bdnomb    = BD_NOMBRE;
	private $bdtipo    = BD_TIPO;
	
	public function __construct() {
		
		$options = array (
				PDO::ATTR_PERSISTENT => false,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION				 
		);
		
		$dsn = $this->bdtipo.':host=' . $this->host . ';dbname=' . $this->bdnomb;		
		//$dsn = BD_TIPO.":host=".BD_HOST.";port=3306;dbname=".BD_NOMBRE;
		
		try {
			$this->con = new PDO( $dsn, $this->user, $this->pass, $options );
			$this->con->exec("set names utf8");
			
		} catch ( PDOException $e ) {
			$this->error =  $e->getMessage ();
		} catch ( Exception $e ) {
			$this->error = $e->getMessage ();
		}
	}
	 
	public function query($query){
		$this->stmt = $this->con->prepare($query);
	}
	
	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	
	public function execute(){
		return $this->stmt->execute();
	}
	
	public function getFilas(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getFila(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function cantFilas(){
		return $this->stmt->rowCount();
	}
	
	public function lastInsertId(){
		return $this->con->lastInsertId();
	}
	
	public function beginTransaction(){
		return $this->con->beginTransaction();
	}
	
	public function endTransaction(){
		return $this->con->commit();
	}
	
	public function cancelTransaction(){
		return $this->con->rollBack();
	}
	
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
	 
	public function error(){
		return $this->error;
	}
	  
	
}	
