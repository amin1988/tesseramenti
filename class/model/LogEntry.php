<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello');

class LogEntry extends Modello {
	
	public function __construct($id) {
		parent::__construct('log', 'idlog', $id);
	}

	/**
	 * 
	 * @return int 
	 */
	public function getIDUtente() {
		return $this->get('idutente');
	}

	/**
	 * 
	 * @return int 
	 */
	public function getLivello() {
		return $this->get('livello');
	}

	/**
	 * 
	 * @return string 
	 */
	public function getNote() {
		return $this->get('note');
	}

	/**
	 * 
	 * @return mixed 
	 */
	public function getDettagli() {
		$val = $this->get('dettagli');
		return unserialize($val);
	}

	/**
	 * 
	 * @return Timestamp 
	 */
	public function getData() {
		return $this->getTimestamp('data');
	}
	
}
