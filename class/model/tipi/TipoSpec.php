<?php
if (!defined('_BASE_DIR_')) exit();

define('_TIPI_DIR_', _CLASS_DIR_.'model/tipi/');

class TipoSpec {
	
	/**
	 * Restituisce i gradi che possono essere modificati dopo il pagamento
	 * @param int $idgrado il grado attuale
	 * @return int[]|NULL l'array di ID dei gradi che possono essere impostati
	 * o NULL se non è possibile modificare il grado
	 */
	public function getGradiModificabili($idgrado) {
		return NULL;
	}
	
	/**
	 * Restituisce, se esistono, i dati extra di una certa qualifica
	 * @param Qualifica $qualifica
	 * @return DatiExtra|NULL
	 */
	public function getDatiExtra($qualifica) {
		return NULL;
	}
}

class TipoSpecUtil {
	private static $inst = NULL;
	
	/**
	 * Registro delle classi TipoSpec per ogni tipo
	 * @var TipoSpec[]
	 */
	private $tab = array();
	
	/**
	 * @return TipoSpecUtil 
	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new TipoSpecUtil();
		return self::$inst;
	}
	
	/**
	 * Restituisce l'oggetto TipoSpec relativo ad un certo tipo
	 * @param int $idtipo
	 * @return TipoSpec
	 */
	public function spec($idtipo) {
		if (!isset($this->tab[$idtipo])) {
			$file = _TIPI_DIR_ . $idtipo . '.php';
			if (file_exists($file)){
				//include il file, che deve aggiungere la classe da solo
				require_once $file;
			} else {
				//usa la classe di default
				$this->_addTipoSpec($idtipo, 'TipoSpec');
			}
		}
		return $this->tab[$idtipo];
	}
	
	/**
	 * Imposta la classe TipoSpec da utilizzare per un certo tipo 
	 * @param int $idtipo ID del tipo
	 * @param string $classe nome della classe che estende TipoSpec
	 */
	public function _addTipoSpec($idtipo, $classe) {
		$this->tab[$idtipo] = new $classe();
	}
}
