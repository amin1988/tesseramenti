<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Modello');

class Federazione extends Modello {
	
	const FEDER_FIAM = 1;
	const FEDER_ETSIA = 2;
	
	/**
	 * Restituisce la federazione associata ad un certo id
	 * @param int $id l'id della federazione
	 * @return Federazione o NULL se non esiste nessuna federazione con l'id specificato
	 */
	public static function fromId($id) {
		return ModelFactory::get(__CLASS__)->fromId($id);
	}
	
	/**
	 * 
	 * @return Federazione[]
	 */
	public static function elenco()
	{
		$db = Database::get();
		
		$rs = $db->select('federazione');
		
		return ModelFactory::listaSql('Federazione', $rs);
	}
	
	public function __construct($id=NULL) {
		parent::__construct('federazione', 'id', $id);
	}
	
	public function __toString()
	{
		return $this->getNome();
	}

	/**
	 * 
	 * @return string 
	 */
	public function getNome() {
		return $this->get('nome');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setNome($val) {
		$this->set('nome', $val);
	}

}
