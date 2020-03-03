<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Modello');

class Tipo extends Modello {
	private $_spec=NULL;
	
	/**
	 * Restituisce il tipo associato ad un certo id
	 * @param int $id l'id del tipo
	 * @return Tipo o NULL se non esiste nessun tipo con l'id specificato
	 */
	public static function fromId($id) {
		return ModelFactory::get(__CLASS__)->fromId($id);
	}
	
	/**
	 * Restituisce i tipi inseriti nel database
	 * @return Tipo[]
	 */
	public static function getTipi() {
		return ModelFactory::get(__CLASS__)->listaCompleta('tipi', 'idtipo');
	}
	
	/**
	 * Restituisce i tipi per un settore
	 * @param integer $idsettore
	 * @return Tipo[]
	 */
	public static function getFromSettore($idsettore) {
		$rs = Database::get()->select('tipi',"idsettore='$idsettore'");
		return ModelFactory::get(__CLASS__)->listaSql('Tipo', $rs);
	}
	
	function __construct($id = NULL) {
		parent::__construct('tipi', 'idtipo', $id);
	}

	function getNome() {
		return $this->get('nome');
	}

	/**
	 * Restituisce il nome del tipo al plurale
	 * @return string
	 */
	function getPlurale() {
		return $this->get('plurale');
	}
	
	function getIDSettore() {
		return $this->get('idsettore');
	}
	
	/**
	 * Restituisce i gradi che possono essere modificati dopo il pagamento
	 * @param int $idgrado il grado attuale
	 * @return int[]|NULL l'array di ID dei gradi che possono essere impostati
	 * o NULL se non è possibile modificare il grado
	 */
	public function getGradiModificabili($idgrado) {
		return $this->getSpec()->getGradiModificabili($idgrado);
	}
	
	/**
	 * Restituisce, se esistono, i dati extra di una certa qualifica
	 * @param Qualifica $qualifica
	 * @return DatiExtra|NULL
	 */
	public function getDatiExtra($qualifica) {
		return $this->getSpec()->getDatiExtra($qualifica);
	}
	
	/**
	 * @return TipoSpec
	 */
	protected function getSpec() {
		if ($this->_spec === NULL) {
			include_model('tipi/TipoSpec');
			$this->_spec = TipoSpecUtil::get()->spec($this->getId());
		}
		return $this->_spec;
	}

}