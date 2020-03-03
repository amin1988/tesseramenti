<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello','ModelFactory');

class Provincia extends Modello {
	const TAB = 'province';
	const IDCOL = 'idprovincia';
	
	/**
	 * Restituisce la provincia associata ad un certo id
	 * @param int $id l'id della provincia
	 * @return Provincia o NULL se non esiste nessuna provincia con l'id specificato
	 */
	public static function fromId($id) {
		return ModelFactory::get(__CLASS__)->fromId($id);
	}
	
	/**
	 * Restituisce la provincia associata ad una certa sigla
	 * @param string $sigla la sigla della provincia
	 * @return Provincia o NULL se non esiste nessuna provincia con la sigla specificata
	 */
	public static function fromSigla($sigla) {
		$db = Database::get();
		$sigla = $db->quote(strtoupper($sigla));
		$rs = $db->select(self::TAB, "sigla = '$sigla'");
		return ModelFactory::get(__CLASS__)->singleFromSql($rs, self::IDCOL);
	}
	
	/**
	 * Restituisce tutte le province all'interno di una regione
	 * @param Regione $regione oppure id regione
	 * @return Provincia[]
	 */
	public static function listaRegione($regione) {
		$db = Database::get();
		if (is_object($regione))
			$idr = $regione->getId();
		else
			$idr = $db->quote($regione);
		$rs = $db->select(self::TAB, "idregione = '$idr'");
		return ModelFactory::get(__CLASS__)->listFromSql($rs, self::IDCOL);
	}
	
	/**
	 * Da utilizzare come callback per FormElem_AutoList
	 * @param int $idreg 
	 * @param int $idprov [opz]
	 */
	public static function ajax($idreg, $idprov=NULL) {
		if ($idprov === NULL)
			return self::listaRegione($idreg);
		else {
			$p = self::fromId($idprov);
			if ($p === NULL || $p->getIDRegione() != $idreg)
				return NULL;
			else
				return $p;
		}
	}

	public function __construct($id=NULL) {
		parent::__construct(self::TAB, self::IDCOL, $id);
	}

	/**
	 * Restituisce l'id della regione in cui si trova questa provincia
	 * @return int
	 */
	public function getIDRegione() {
		return $this->get('idregione');
	}
	
	/**
	 * Restituisce il nome della provincia
	 * @return string
	 */
	public function getNome() {
		return $this->get('nome');
	}

	/**
	 * Restituisce la sigla della provincia
	 * @return string
	 */
	public function getSigla() {
		return $this->get('sigla');
	}
	
	function __toString() {
		return $this->get('nome');
	}
}
