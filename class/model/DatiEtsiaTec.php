<?php
if (!defined('_BASE_DIR_')) exit();
include_model('DatiExtra','ModelFactory');

class DatiEtsiaTec extends DatiExtra {
	const TAB = 'karate_dan_etsia';
	const MAX_DAN = 10;
	const MAX_KYU = 9;
	
	private static $inst = array();
	
	/**
	 * @var Qualifica
	 */
	protected $qual;
	
	/**
	 * @param Qualifica $qualifica
	 * @return DatiEtsia
	 */
	public static function fromId($qualifica) {
		$idtess = $qualifica->getIdTesserato();
		if ($idtess === NULL) $idtess = 'NULL';
		
		if (!isset(self::$inst[$idtess]))
			self::$inst[$idtess] = new DatiEtsiaTec($qualifica);
		return self::$inst[$idtess];
	} 
	
	protected function __construct($qualifica) {
		$this->qual = $qualifica;
	}
	
	public function getChiavi() {
		return array('dan');
	}
	
	public function getValori($key) {
		if ($key != 'dan') return NULL;
		$res = array();
		$res[0] = "Member";
		$y = 1;
		for ($i = self::MAX_KYU; $i >= 1; $i--) {
			$res[$y] = "{$i}° Kyu";
			$y++;
		}
		for ($i = 1; $i <= self::MAX_DAN; $i++) {
			$res[$y] = "{$i}° Dan";
			$y++;
		}
		return $res;
	}
	
	public function toString($key) {
		$v = $this->get($key);
		if ($v === NULL) return NULL;
		if ($v == 0) 
			return "Member";
		if ($v >= 1 && $v <= 9)
		{
			$v = self::MAX_KYU + 1 - $v;
			return "{$v}° Kyu";
		}
		else 
		{
			$v -= self::MAX_KYU;
			return "{$v}° Dan";
		}
	}
	
	public function salva() {
		$idtess = $this->qual->getIdTesserato();
		if ($idtess === NULL) return false;
		
		$res = true;
		if ($this->mod['dan']) {
			if ($this->dati['dan'] === NULL) {
				Database::get()->delete(self::TAB, "idtesserato='$idtess'");
				$this->mod['dan'] = false;
			} else {
				$rd = Database::get()->insertUpdate(self::TAB, 
						array('dan' => $this->dati['dan']), 
						array('idtesserato' => $idtess));
				$this->mod['dan'] = !$rd;
				$res &= $rd;
			}
			$this->mod['dan'] = false;
		}
		return $res;
	}
	
	/**
	 * Carica il valore di un dato in $dati e lo restituisce
	 * @param string $key la chiave del dato da caricare
	 * @return mixed
	 */
	protected function carica($key) {
		if ($key != "dan") return;
		$idtess = $this->qual->getIdTesserato();
		if ($idtess === NULL) return;
		
		$v = Database::get()->field(self::TAB, 'dan', "idtesserato='$idtess'");
		
		$this->dati[$key] = $v;
		$this->mod[$key] = false;
		return $v;
	}
	
	public function elimina($key=NULL) {
		$idtess = $this->qual->getIdTesserato();
		if ($idtess === NULL) return;
		return Database::get()->delete(self::TAB, "idtesserato='$idtess'");
	}
}