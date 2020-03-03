<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Modello');

class Tesserini extends Modello {
	
	/**
	 * Restituisce tutti i tesserini già generati per una società
	 * @param int $id_soc
	 * @return Tesserini[]
	 */
	public static function getSocieta($id_soc, $anno)
	{
		$db = Database::get();
		$rs = $db->select('tesserini',"id_societa='$id_soc' AND anno='$anno'");
		
		return ModelFactory::listaSql('Tesserini', $rs);
	}

	public function __construct($id=NULL) {
		parent::__construct('tesserini', 'id', $id);
	}

	/**
	 * 
	 * @return int 
	 */
	public function getIDSocieta() {
		return $this->get('id_societa');
	}

	/**
	 * 
	 * @param int $val
	 */
	public function setIDSocieta($val) {
		$this->set('id_societa', $val);
	}


	/**
	 * 
	 * @return int 
	 */
	public function getIDTesserato() {
		return $this->get('id_tesserato');
	}

	/**
	 * 
	 * @param int $val
	 */
	public function setIDTesserato($val) {
		$this->set('id_tesserato', $val);
	}


	/**
	 * 
	 * @return int 
	 */
	public function getIDTipo() {
		return $this->get('id_tipo');
	}

	/**
	 * 
	 * @param int $val
	 */
	public function setIDTipo($val) {
		$this->set('id_tipo', $val);
	}


	/**
	 * 
	 * @return int 
	 */
	public function getAnno() {
		return $this->get('anno');
	}

	/**
	 * 
	 * @param int $val
	 */
	public function setAnno($val) {
		$this->set('anno', $val);
	}

}
