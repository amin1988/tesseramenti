<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello');

class Grado extends Modello {
	
	/**
	 * Restituisce il grado associato ad un certo id
	 * @param int $id l'id del grado
	 * @return Grado o NULL se non esiste nessun grado con l'id specificato
	 */
	public static function fromId($id) {
		return ModelFactory::get(__CLASS__)->fromId($id);
	}
	
	/**
	 * Restituisce la lista di gradi possibili per un tipo
	 * @param integer $idt
	 * @return Grado[]
	 */
	public static function listaTipo($idt) {
		$rs = Database::get()->select('gradi',"idtipo='$idt' ORDER BY idgrado ASC");
		return ModelFactory::get(__CLASS__)->listFromSql($rs, 'idgrado');
	}
	
	/**
	 * @deprecated sostituista da __toString
	 * Restituisce la stringa associata al grado
	 * @param Grado $grado
	 * @return string
	 */
	public static function toStringStatic($grado) {
		return $grado->getNome();
	}
	
	function __construct($id = NULL) {
		parent::__construct('gradi', 'idgrado', $id);
	}
	
	function getNome() {
		return $this->get('nome');
	}
	
	function getIDTipo() {
		return $this->get('idtipo');
	}
	
	function getPrezzo() {
		return $this->get('prezzo');
	}
	
	function getPrezzoEuro() {
		return $this->get('prezzo')/100.0;
	}
	
	function __toString() {
		return $this->get('nome');
	}
}