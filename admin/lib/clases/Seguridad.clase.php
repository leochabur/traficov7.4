<?php
class Seguridad{
	

	public static function randomSalt($len = 8) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#$%^&*()-=_+';
		$l = strlen($chars) - 1;
		$str = '';
		for ($i = 0; $i < $len; ++$i) {
			$str .= $chars[rand(0, $l)];
		}
		return $str;
	}
	
	public static function create_hash($string, $hash_method = 'sha1') {
		 
		if (function_exists('hash') && in_array($hash_method, hash_algos())) {
			return hash($hash_method,   $string);
		}
		return sha1( $string);
	}
	
	/**
	 * @param string $pass The user submitted password
	 * @param string $hashed_pass The hashed password pulled from the database
	 * @param string $salt The salt pulled from the database
	 * @param string $hash_method The hashing method used to generate the hashed password
	 */
	public static function validateHashedPass($pass, $hashed_pass, $salt, $hash_method = 'sha1') {
	
		if (function_exists('hash') && in_array($hash_method, hash_algos())) {
			return ($hashed_pass === hash($hash_method, $salt . $pass));
		}
		return ($hashed_pass === sha1($salt . $pass));
	}
	
	public static function hash_password($string) {			
		 
		return password_hash($string, PASSWORD_DEFAULT);
	}

	public static function validaPasswordHash($string, $hashed) {
			 
		return password_verify($string, $hashed);
	}
	
}