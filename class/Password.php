<?php
if (!defined('_BASE_DIR_')) exit();

class Password {
	private static $inst = NULL;
	
	/**
	 * @return Password
	 */
	public static function get() {
		if (self::$inst === NULL)
			self::$inst = new Password();
		return self::$inst;
	}
	
	protected function __construct() {}
	
	/**
	 * Restituisce la versione criptata della password
	 * @param string $psw la password in chiaro
	 * @return string la password criptata
	 */
	public function cripta($psw) {
		return md5($psw);
	}
	
	/**
	 * Indica se la password in chiaro corrisponde a quella criptata
	 * @param string $chiaro la password in chiaro
	 * @param string $cript la password criptata
	 * @return boolean true se le password corrispondono
	 */
	public function verifica($chiaro, $cript) {
		return $cript == md5($chiaro);
	}
	
	/**
	 * Genera una nuova password
	 * @param string $seed [opz] un valore da usare per generare la password
	 * @return string la password generata
	 */
	public function genera($seed='') {
		$p = md5(time() . $seed . rand());
		return substr($p, 1, rand(8, 10));
	}
}