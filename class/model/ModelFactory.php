<?php
if (!defined("_BASE_DIR_")) exit();
include_class('Database');

class ModelFactory {
	private static $fact = array();
	
	private $classe;
	private $val = array();
	private $full = false;
	
	/**
	 * Rispistina lo stato. Usare per i test
	 */
	public static function reset() {
		self::$fact = array();
	}
	
	/**
	 * Restituisce una lista di modelli a partire dai loro id
	 * @param string $classe il nome della classe del modello da creare
	 * @param array $lista_id l'elenco degli id da caricare negli oggetti
	 * @return array un array di oggetti del tipo id => $classe
	 */
	public static function lista($classe, $lista_id) {
		if (!is_array($lista_id) || count($lista_id) == 0) return array();
		$res = array();
		foreach ($lista_id as $id) {
			$res[$id] = new $classe($id);
		}
		return $res;
	}
	
	/**
	 * Restituisce una lista di modelli a partire dal risultato di una query
	 * @param string $classe il nome della classe del modello da creare
	 * @param DbResult $rs il resultset da cui prendere i valori
	 * @return un array di oggetti del tipo id => $classe
	 */
	public static function listaSql($classe, $rs) {
		if ($rs === NULL) return array();
		$res = array();
		while($row = $rs->fetch_assoc()) {
			/* @var $o Modello */
			$o = Modello::_creaConDati($classe, $row);
			$res[$o->getId()] = $o;
		}
		return $res;
	}
	
	/**
	 * Restituisce il ModelFactory di una certa classe
	 * @param string $classe il nome della classe
	 * @return ModelFactory
	 */
	public static function get($classe) {
		if (isset(self::$fact[$classe])) 
			return self::$fact[$classe];
		
		$f = new ModelFactory($classe);
		self::$fact[$classe] = $f;
		return $f;
	}
	
	protected function __construct($classe) {
		$this->classe = $classe;
	} 
	
	/**
	 * Restituisce l'elenco completo dei modelli presenti nel database
	 * @param string $tab la tabella da cui leggere i modelli 
	 * @param string $idcol il nome della colonna della chiave
	 * @return Modello[] formato id => valore
	 */
	public function listaCompleta($tab, $idcol) {
		if (!$this->full) {
			$this->listFromSql(Database::get()->select($tab), $idcol, true);
			$this->full = true;
		}
		return $this->val;
	}
	
	/**
	 * Restituisce il modello associato ad un certo id
	 * @param string $id l'id da cercare
	 * @return Modello o NULL se non esiste nessun modello con l'id specificato
	 */
	public function fromId($id) {
		if (isset($this->val[$id])) 
			return $this->val[$id];
		
		$o = new $this->classe($id);
		if (!$o->esiste()) 
			$o = NULL;
		else
			$this->val[$id] = $o;
		return $o;
	}

	/**
	 * Restituisce il modello restituito da una certa query
	 * @param DbResult $rs la query
	 * @param string $idcol il nome della colonna chiave
	 * @param boolean $carica [def: true] true per caricare i dati del modello
	 * a partire dal contenuto della query
	 * @param boolean $cache [def: true] per utilizzare la cache del factory 
	 * @return Modello
	 */
	public function singleFromSql($rs, $idcol, $carica=true, $cache=true) {
		$row = $rs->fetch_assoc();
		if ($row === NULL) return NULL;
		$id = $row[$idcol];
		if ($cache && isset($this->val[$id]))
			return $this->$val[$id];
	
		if ($carica) {
			$o = Modello::_creaConDati($this->classe, $row);
		} else 
			$o = new $this->classe($id);
		if ($cache) $this->val[$id] = $o;
		return $o;
	}
		
	/**
	 * Restituisce la lista di modelli restituiti da una certa query
	 * @param DbResult $rs la query
	 * @param string $idcol il nome della colonna chiave
	 * @param boolean $carica [def: true] true per caricare i dati dei modello
	 * a partire dal contenuto della query
	 * @param boolean $cache [def: true] per utilizzare la cache del factory 
	 * @return Modello
	 */
	public function listFromSql($rs, $idcol, $carica = true, $cache = true) {
		$res = array();
		while($row = $rs->fetch_assoc()) {
			$id = $row[$idcol];
			if ($cache && isset($this->val[$id])) {
				$o = $this->val[$id];
			} else {
				if ($carica) {
					$o = Modello::_creaConDati($this->classe, $row);
				} else 
					$o = new $this->classe($id);
				if ($cache) $this->val[$id] = $o;
			}
			$res[$id] = $o;
		}
		return $res;
	}
}