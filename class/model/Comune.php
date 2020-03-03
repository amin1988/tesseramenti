<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello','ModelFactory');

class Comune extends Modello {
	const TAB = 'comuni';
	const IDCOL = 'idcomune';
	
	/**
	 * Restituisce il comune associato ad un certo id
	 * @param int $id l'id del comune
	 * @return Comune o NULL se non esiste nessuna comune con l'id specificato
	 */
	public static function fromId($id) {
		return ModelFactory::get(__CLASS__)->fromId($id);
	}
	
	/**
	 * Restituisce il comune associato ad un certo codice catastale
	 * @param string $codice il codice del comune
	 * @return Comune o NULL se non esiste nessun comune con il codice specificato
	 */
	public static function fromCodice($codice) {
		$db = Database::get();
		$codice = $db->quote(strtoupper($codice));
		$rs = $db->select(self::TAB, "codice = '$codice'");
		return ModelFactory::get(__CLASS__)->singleFromSql($rs, self::IDCOL);
	}
	
	/**
	 * Restituisce tutti i comuni all'interno di una provincia
	 * @param Provincia $province oppure id provincia
	 * @return Comune[]
	 */
	public static function listaProvincia($provincia) {
		$db = Database::get();
		if (is_object($provincia))
			$idr = $provincia->getId();
		else
			$idr = $db->quote($provincia);
		$rs = $db->select(self::TAB, "idprovincia = '$idr'");
		return ModelFactory::get(__CLASS__)->listFromSql($rs, self::IDCOL);
	}
	
	/**
	 * Da utilizzare come callback per FormElem_AutoList
	 * @param int $idprov
	 * @param int $idcom [opz]
	 */
	public static function ajax($idprov, $idcom=NULL) {
		if ($idcom === NULL)
			return self::listaProvincia($idprov);
		else {
			$c = self::fromId($idcom);
			if ($c === NULL || $c->getIDProvincia() != $idprov)
				return NULL;
			else
				return $c;
		}
	}

	public function __construct($id=NULL) {
		parent::__construct(self::TAB, self::IDCOL, $id);
	}

	/**
	 * Restituisce l'id della provincia in cui si trova questo comune
	 * @return int 
	 */
	public function getIDProvincia() {
		return $this->get('idprovincia');
	}

	/**
	 * Restituisce il nome del comune
	 * @return string 
	 */
	public function getNome() {
		return $this->get('nome');
	}

	/**
	 * Restituisce il codice catastale del comune
	 * @return string 
	 */
	public function getCodice() {
		return $this->get('codice');
	}
	
	public function __toString()
	{
		return $this->get('nome');
	}
}
