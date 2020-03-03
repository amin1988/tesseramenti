<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Modello');

class Crediti extends Modello {
	const TAB = 'crediti';
	
	/**
	 * @param int $idtess
	 * @param int $idsett
	 * @param string $desc
	 * @param Data $data
	 * @param int $crediti
	 * @return Crediti
	 */
	public static function crea($idtess, $idsett, $desc, $data, $crediti) {
		$c = new Crediti();
		$c->set('idtesserato', $idtess);
		$c->set('idsettore', $idsett);
		$c->set('descrizione', $desc);
		$c->setData('data', $data);
		$c->set('crediti', $crediti);
		return $c;
	}

	public function __construct($id=NULL) {
		parent::__construct(self::TAB, 'idcrediti', $id);
	}

	/**
	 * 
	 * @return int 
	 */
	public function getIDTesserato() {
		return $this->get('idtesserato');
	}

	/**
	 * @deprecated
	 */
	public function getIDTipo() {
		return $this->getIDSettore();
	}

	/**
	 * 
	 * @return int 
	 */
	public function getIDSettore() {
		return $this->get('idsettore');
	}
	
	/**
	 * 
	 * @return string 
	 */
	public function getDescrizione() {
		return $this->get('descrizione');
	}

	/**
	 * 
	 * @return Data 
	 */
	public function getDataAss() {
		return $this->getData('data');
	}

	/**
	 * 
	 * @return int 
	 */
	public function getCrediti() {
		return $this->get('crediti');
	}
	
	public function elimina() {
		$this->logValori(E_INFO, 'Crediti eliminati');
		parent::elimina();
	}
}

class CreditiUtil {
	private static $inst = NULL;
	
	/**
	 * @return CreditiUtil 
	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new CreditiUtil();
		return self::$inst;
	}
	
	/**
	 * Restituisce il totale dei crediti di un tesserato per ogni tipo
	 * @param int $idtess
	 * @return int[] formato: idtipo => crediti
	 */
	public function getTotale($idtess) {
		$rs = Database::get()->select(Crediti::TAB, "idtesserato = '$idtess' GROUP BY idtipo", 
				'idtipo, SUM(crediti) as tot');
		$res = array();
		while ($row = $rs->fetch_assoc()) {
			$res[$row['idtipo']] = $row['tot'];
		}
		return $res;
	}
	
	/**
	 * Restituisce il totale dei crediti di un tesserato per un certo tipo
	 * @param int $idtess
	 * @param int $idtipo
	 * @return int
	 */
	public function getTotaleSettore($idtess, $idsett) {
		return Database::get()->field(Crediti::TAB, 'SUM(crediti)', "idtesserato='$idtess' AND idtipo='$idtipo'");
	}
	
	/**
	 * Restituisce l'elenco dei crediti di un tesserato ordinati per data decrescente
	 * @param int $idtess
	 * @param int $idsett [opz] ID del settore di cui leggere i crediti
	 * @return Crediti[][] formato idsettore => Crediti[] <br>
	 * oppure Crediti[] se $idsett !== NULL
	 */
	public function getListaTess($idtess, $idsett=NULL) {
		$where = "idtesserato = '$idtess'";
		if ($idsett !== NULL) 
			$where .= " AND idsettore='$idsett'";
		$rs = Database::get()->select(Crediti::TAB, "$where ORDER BY data DESC, idcrediti DESC");
		
		$res = array();
		while($row = $rs->fetch_assoc()) {
			/* @var $c Crediti */
			$c = Modello::_creaConDati('Crediti', $row);
			if ($idsett !== NULL)
				$res[] = $c;
			else
				$res[$c->getIDSettore()][] = $c;
		}
		return $res;
	}
}
