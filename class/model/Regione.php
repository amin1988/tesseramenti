<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello','ModelFactory');

class Regione extends Modello {
	const TAB = 'regioni';
	const IDCOL = 'idregione';
	
	/**
	 * Restituisce l'elenco di tutte le regioni
	 * @return Regione[]
	 */
	public static function listaCompleta() {
		return ModelFactory::get(__CLASS__)->listaCompleta(self::TAB, self::IDCOL);
	}
	
	/**
	 * Restituisce la regione associata ad un certo id
	 * @param int $id l'id della provincia
	 * @return Provincia o NULL se non esiste nessuna provincia con l'id specificato
	*/
	public static function fromId($id) {
		return ModelFactory::get(__CLASS__)->fromId($id);
	}
	
	public function __construct($id=NULL) {
		parent::__construct(self::TAB, self::IDCOL, $id);
	}

	/**
	 * Restituisce il nome della regione
	 * @return string 
	 */
	public function getNome() {
		return $this->get('nome');
	}
	
	function __toString() {
		return $this->get('nome');
	}
}
