<?php

// configuración
require_once ('bdconexion.config.php');

//  Clase Base de datos 
class BdConexion {
	 
	private static $con;
	
	public static function bdInstancia() {
		
		if (! self::$con) {
			new BdConexion();
		}
		return self::$con;
	}
	
	private function __construct() {
		$options = array (
				PDO::ATTR_PERSISTENT => false,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' 
		);
		
		$dsn = BD_TIPO.":host=".BD_HOST.";port=3306;dbname=".BD_NOMBRE;
		
		try {
			self::$con = new PDO( $dsn, BD_USER,BD_PASS, $options );
			self::$con->exec("SET CHARACTER SET utf8");
			
		} catch ( PDOException $e ) {
			echo $e->getMessage ();
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	 
	
	
	public function select($table, $where="", $bind="", $fields="*") {
		$sql = "SELECT " . $fields . " FROM " . $table;
		if(!empty($where))
			$sql .= " WHERE " . $where;
		$sql .= ";";
		return $this->run($sql, $bind);
	}
	
	public function update($table, $info, $where, $bind="") {
		$fields = $this->filter($table, $info);
		$fieldSize = sizeof($fields);
	
		$sql = "UPDATE " . $table . " SET ";
		for($f = 0; $f < $fieldSize; ++$f) {
			if($f > 0)
				$sql .= ", ";
			$sql .= $fields[$f] . " = :update_" . $fields[$f];
		}
		$sql .= " WHERE " . $where . ";";
	
		$bind = $this->cleanup($bind);
		foreach($fields as $field)
			$bind[":update_$field"] = $info[$field];
	
		return $this->run($sql, $bind);
	}
	
	public function insert($table, $info) {
		$fields = $this->filter($table, $info);
		$sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
		$bind = array();
		foreach($fields as $field)
			$bind[":$field"] = $info[$field];
		return $this->run($sql, $bind);
	}
	
	public function delete($table, $where, $bind="") {
		$sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
		$this->run($sql, $bind);
	}
	
	
	public function run($sql, $bind="") {
		$this->sql = trim($sql);
		$this->bind = $this->cleanup($bind);
		$this->error = "";
	
		try {
			$conn = $this->bdInstancia();
			
			$pdostmt = $conn->prepare($this->sql);
			if($pdostmt->execute($this->bind) !== false) {
				if(preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->sql))
					return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
				elseif(preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->sql))
				return $pdostmt->rowCount();
			}
		} catch (PDOException $e) {
			//$this->error = $e->getMessage();
			 
			return false;
		}
	}
	
	private function filter($table, $info) {
		$conn = $this->bdInstancia();
		$driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
		if($driver == 'sqlite') {
			$sql = "PRAGMA table_info('" . $table . "');";
			$key = "name";
		}
		elseif($driver == 'mysql') {
			$sql = "DESCRIBE " . $table . ";";
			$key = "Field";
		}
		else {
			$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
			$key = "column_name";
		}
	
		if(false !== ($list = $this->run($sql))) {
			$fields = array();
			foreach($list as $record)
				$fields[] = $record[$key];
			return array_values(array_intersect($fields, array_keys($info)));
		}
		return array();
	}
	
	private function cleanup($bind) {
		if(!is_array($bind)) {
			if(!empty($bind))
				$bind = array($bind);
			else
				$bind = array();
		}
		return $bind;
	}
		
	private function error(){
		
	}
	  
	
}	
