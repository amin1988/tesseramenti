<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello','ModelFactory');

class Pagamento extends Modello {
	
	public static function creaScadenza($oggi=NULL) {
		if($oggi === NULL) $oggi = DataUtil::get()->oggi();
		
		$anno = $oggi->getAnno();
		if(in_rinnovo($oggi)) $anno++;
		return new Data($anno, 12, 31);
	}
	
	/**
	 * Crea un nuovo pagamento per un settore
	 * @param int $idsoc
	 * @param Settore $settore
	 * @param Data $scadenza
	 * @return Pagamento
	 */
	public static function creaSettore($idsoc, $settore, $scadenza=NULL) {
		$p = new Pagamento();
		$p->set('idsocieta', $idsoc);
		$p->set('idsettore', $settore->getId());
		$p->set('quota', $settore->getPrezzo());
		if ($scadenza === NULL) 
			$scadenza = self::creaScadenza();
		$p->setData('scadenza', $scadenza);
		
		return $p;
	}
	
	/**
	 * 
	 * @param Tesserato $tesserato
	 * @param Grado $grado
	 * @param Data $scadenza
	 */
	public static  function creaQualifica($tesserato, $grado, $scadenza=NULL) {
		$p = new Pagamento();
		
		$p->set('idsocieta', $tesserato->getIDSocieta());
		$p->set('idtesserato', $tesserato->getId());
		$p->set('idtipo', $grado->getIDTipo());
		$p->set('idgrado', $grado->getId());
		if ($scadenza === NULL)
			$scadenza = self::creaScadenza();
		$p->setData('scadenza', $scadenza);
		$p->set('quota', $grado->getPrezzo());
		
		return $p;
	}
	
	/**
	 * Restituisce il pagamento non scaduto di una società per un settore
	 * @param integer $idsoc
	 * @param integer $idsett oppure oggetto Settore
	 * @param int $anno l'anno corrente
	 * @return Pagamento oppure NULL se non c'è nessun pagamento non scaduto
	 */
	public static function getCorrenteSettore($idsoc, $idsett, $anno=NULL) {
		if ($anno === NULL) $anno = date('Y');
		$db = Database::get();
		if(is_object($idsett))
			$idsett = $idsett->getId();  
		
		$anno = $db->quote($anno);
		$rq = $db->select('pagamenti_correnti',"idsocieta='$idsoc' AND idsettore='$idsett' AND YEAR(scadenza)='$anno'");
		$row = $rq->fetch_assoc();
		
		if($row === NULL) return NULL;
		
		$p = new Pagamento();
		$p->carica($row);
		
		return $p;
	}
	
	public static function getCorrenteTipo($idtesserato, $idtipo) {
		$db = Database::get();
		
		$rq = $db->select('pagamenti_correnti',"idtesserato='$idtesserato' AND idtipo='$idtipo'");
		$row = $rq->fetch_assoc();
		
		if($row === NULL) return NULL;
		
		$p = new Pagamento();
		$p->carica($row);
		
		return $p;
	}
	
	public static function haCorrenti($idtesserato) {
		$db = Database::get();
		
		$rq = $db->select('pagamenti_correnti',"idtesserato='$idtesserato'");
		$row = $rq->fetch_row();
		
		if($row !== NULL) return true;
		else return false;
	}
	
	/**
	 * 
	 * @param int $idtesserato
	 * @return Pagamento[]
	 */
	public static function getCorrenti($idtesserato) {
		$db = Database::get();
	
		$rs = $db->select('pagamenti_correnti',"idtesserato='$idtesserato'","idpagamento");
		
		while($row = $rs->fetch_row())
			$lista[] = $row[0];
		
		return ModelFactory::lista('Pagamento', $lista);
// 		return ModelFactory::listaSql('Pagamento', $rs);
	}
	

	public static function haPagati($idtesserato) {
		$db = Database::get();
	
		$rq = $db->select('pagamenti_correnti',"idtesserato='$idtesserato' AND data_pagamento IS NOT NULL");
		$row = $rq->fetch_row();
	
		if($row !== NULL) return true;
		else return false;
	}
	
	public static function numTessCorrenti($idsocieta) {
		$db = Database::get();
		
		$rq = $db->select('pagamenti_correnti',"idsocieta='$idsocieta' AND idtesserato IS NOT NULL AND data_pagamento IS NOT NULL","DISTINCT(idtesserato)");
		
		$num = 0;
		while($row = $rq->fetch_row())
			$num++;
		
		return $num;
	}
	
	public static function numSettCorrenti($idsocieta) {
		$db = Database::get();
		
		$rq = $db->select('pagamenti_correnti',"idsocieta='$idsocieta' AND idsettore IS NOT NULL AND idtesserato IS NULL AND data_pagamento IS NOT NULL","DISTINCT(idsettore)");
		
		$num = 0;
		while($row = $rq->fetch_row())
			$num++;
		
		return $num;
	}
	
	function __construct($id = NULL) {
		parent::__construct('pagamenti', 'idpagamento', $id);
	}
	
	function getIdSocieta() {
		return $this->get('idsocieta');
	}
	
	function getIdSettore() {
		return $this->get('idsettore');
	}
	
	function getIdTesserato() {
		return $this->get('idtesserato');
	}
	
	function getIdTipo() {
		return $this->get('idtipo');
	}
	
	function getQuota() {
		return $this->get('quota');
	}
	
	function getQuotaEuro() {
		return $this->get('quota')/100.0;
	}
	
	function getDataCreazione() {
		return $this->getTimestamp('data_creazione');
	}
	
	function getDataPagamento() {
		return $this->getTimestamp('data_pagamento');
	}
	
	function getDataScadenza() {
		return $this->getData('scadenza');
	}
	
	function isPagato() {
		return $this->getDataPagamento()!==NULL;
	}
	
	function setPagato($pagato = true) {
		if($pagato)
			$ts = TimestampUtil::get()->adesso();
		else 
			$ts = NULL;
		$this->setTimestamp('data_pagamento', $ts);
	}
	
	/**
	 * Nello spostamento del tesserato cambio il possessore dell'ultimo pagamento del tesserato
	 */
	function setIdSocieta($val)
	{
		$this->set('idsocieta', $val);
	}
	
	/**
	 * Indica se questo pagamento è un pagamento relativo al prossimo anno
	 */
	public function isRinnovo() {
		return $this->getDataScadenza()->getAnno() > date('Y');
	}
	
	public function salva()
	{
		if($this->isMod('data_pagamento') && !$this->isPagato())
			$this->logValori(E_INFO, "pagamento annullato");
		
		return parent::salva();
	}
	
	public function elimina()
	{
		if($this->isPagato())
			$this->logValori(E_WARNING, "pagamento pagato eliminato");
		
		return parent::elimina();
	}
	
}

class PagamentoUtil {
	private static $inst = NULL;
	
	public static function get() {
		if (self::$inst === NULL)
			self::$inst = new PagamentoUtil();
		return self::$inst;
	}
	
	protected function __construct() {}
	
	/**
	 * Aggiorna i pagamenti dei settori di una società per un certo anno 
	 * @param Societa $soc 
	 * @param int $anno l'anno di cui considerare i pagamenti
	 * @return bool false se ci sono stati errori, true altrimenti
	 */
	public function aggiornaSettori($soc, $anno) {
		$res = true;
		
		$idsoc = $soc->getId();
		$db = Database::get();
		$anno = $db->quote($anno);
		$rs = $db->select('pagamenti',"idsocieta='$idsoc' AND idsettore IS NOT NULL AND YEAR(scadenza)='$anno'");
		$pdb = ModelFactory::listaSql('Pagamento', $rs);
		
		$pl = array();
		$sett = $soc->getIDSettori();
		//elimina i pagamenti per i settori non selezionati
		foreach ($pdb as $p) {
			/* @var $p Pagamento */
			if (!in_array($p->getIdSettore(), $sett))
				$p->elimina();
			else
				$pl[$p->getIdSettore()] = $p;
		}
		
		//crea nuovi pagamenti
		$scad = new Data($anno, 12, 31);
		foreach ($sett as $idsett) {
			if (!isset($pl[$idsett])) {
				$p = Pagamento::creaSettore($idsoc, Settore::fromId($idsett), $scad);
				if ($p->salva()) {
					//elimina i pagamenti non pagati degli anni precedenti
					$db->delete('pagamenti', "idsocieta='$idsoc' AND idsettore='$idsett' AND data_pagamento IS NULL AND YEAR(scadenza)<'$anno'");
				} else 
					$res = false;
			}
		}
		
		return $res;
	}
	
	/**
	 * Restitusice l'elenco dei settori di una società che sono stati rinnovati
	 * @param int $idsoc ID della società
	 * @return int[] ID dei settori rinnovati
	 */
	public function settoriRinnovati($idsoc) {
		$rs = Database::get()->select('pagamenti_rinnovati',"idsocieta='$idsoc' AND idsettore IS NOT NULL", 'idsettore');
		$res = array();
		while ($row = $rs->fetch_row())
			$res[] = $row[0];
		return $res;
	}
	
	/**
	 * Restituisce l'elenco dei settori di una società per i quali esiste un pagamento per l'anno in corso
	 * @param int $idsoc ID della società
	 * @return int[] ID dei settori per i quali esiste il pagamento
	 */
	public function settoriPagati($idsoc) {
		$rs = Database::get()->select('pagamenti_correnti',"idsocieta='$idsoc' AND idsettore IS NOT NULL", 'idsettore');
		$res = array();
		while ($row = $rs->fetch_row())
			$res[] = $row[0];
		return $res;
	}

	/**
	 * Restitusice l'elenco delle qualifiche di un tesserato che sono state rinnovate
	 * @param int $idt ID del tesserato
	 * @return int[] ID delle qualifiche rinnovate
	 */
	public function qualificheRinnovate($idt) {
		$rs = Database::get()->select('pagamenti_rinnovati',"idtesserato='$idt'", 'idtipo');
		$res = array();
		while ($row = $rs->fetch_row())
			$res[] = $row[0];
		return $res;
	}
	
	/**
	 * Restituisce l'ultimo pagamento non pagato e non scaduto
	 * @param int $idtesserato
	 * @param int $idtipo
	 */
	public function tipoNonPagato($idtesserato, $idtipo) {
		$rs = Database::get()->select('pagamenti_correnti',
				"idtesserato='$idtesserato' AND idtipo='$idtipo' AND data_pagamento IS NULL ".
				'ORDER BY scadenza DESC LIMIT 1');
		$row = $rs->fetch_assoc();
		if ($row === NULL) return NULL;
		
		return Modello::_creaConDati('Pagamento', $row);
	}
	
	/**
	 * Restituisce l'ultimo pagamento di un settore non scaduto
	 * @param int $idsoc
	 * @param int $idsett
	 * @return Pagamento
	 */
	public function ultimoSettore($idsoc, $idsett) {
		$rs = Database::get()->select('pagamenti_correnti',
				"idsocieta='$idsoc' AND idsettore='$idsett' ".
				'ORDER BY scadenza DESC LIMIT 1');
		$row = $rs->fetch_assoc();
		if ($row === NULL) return NULL;
		
		return Modello::_creaConDati('Pagamento', $row);
	}
	
	/**
	 * Restituisce l'ultimo pagamento di un tipo non scaduto
	 * @param int $idtesserato
	 * @param int $idtipo
	 * @return Pagamento
	 */
	public function ultimoTipo($idtesserato, $idtipo) {
		$rs = Database::get()->select('pagamenti_correnti',
				"idtesserato='$idtesserato' AND idtipo='$idtipo' ".
				'ORDER BY scadenza DESC LIMIT 1');
		$row = $rs->fetch_assoc();
		if ($row === NULL) return NULL;
		
		return Modello::_creaConDati('Pagamento', $row);
	}
	
	/**
	 * Indica se una società ha dei pagamenti in corso per il prossimo anno
	 * @param int $idsoc
	 * @return boolean
	 */
	public function haSettoriRinnovati($idsoc) {
		$rs = Database::get()->select('pagamenti_rinnovati', "idsocieta='$idsoc' AND idsettore IS NOT NULL LIMIT 1");
		return $rs->fetch_row() !== NULL;
	}
	
	/**
	 * Indica se una società ha dei pagamenti in corso per questo anno
	 * @param int $idsoc
	 * @return boolean
	 */
	public function haSettoriInPagamento($idsoc) {
		$rs = Database::get()->select('pagamenti_correnti', "idsocieta='$idsoc' AND idsettore IS NOT NULL LIMIT 1");
		return $rs->fetch_row() !== NULL;
	}
	
	/**
	 * Restituisce il totale dei pagamenti non pagati di una società
	 * @param int $idsoc ID della società
	 * @return int
	 */
	public function getTotale($idsoc) {
		$db = Database::get();
		$tot = 0;
		//pagamenti dei settori non pagati
		$totsett = $db->field('pagamenti_correnti', 'quota',
				"idsocieta='$idsoc' AND data_pagamento IS NULL AND idsettore IS NOT NULL");
		//seleziona tutti i pagamenti non pagati dei tipi per il cui settore esiste un pagamento
		$tottipi = $db->field('pagamenti_correnti pt INNER JOIN tipi t USING(idtipo) '.
					'INNER JOIN pagamenti_correnti ps ON(t.idsettore=ps.idsettore AND '.
					'pt.idsocieta=ps.idsocieta AND ps.scadenza >= pt.scadenza)',
				'SUM(pt.quota)',
				"pt.idsocieta='$idsoc' AND pt.data_pagamento IS NULL AND pt.idtipo IS NOT NULL");
		//TODO verificare che non ci siano tipi che appaionon due volte
		if ($totsett !== NULL) 
			$tot += $totsett;
		if ($tottipi !== NULL)
			$tot += $tottipi;
		return $tot;
	} 
	
	/**
	 * Restituisce l'elenco di tutti i pagamenti correnti non pagati di una società
	 * @param int $idsoc ID della società
	 * @return Pagamento[]
	 */
	public function nonPagati($idsoc) {
		$db = Database::get();
		//pagamenti dei settori non pagati
		$rs = $db->select('pagamenti_correnti', 
				"idsocieta='$idsoc' AND data_pagamento IS NULL AND idsettore IS NOT NULL");
		$sett = ModelFactory::listaSql('Pagamento', $rs);
		
		//seleziona tutti i pagamenti non pagati dei tipi per il cui settore esiste un pagamento
		$rs = $db->select('pagamenti_correnti pt INNER JOIN tipi t USING(idtipo) '.
					'INNER JOIN pagamenti_correnti ps ON(t.idsettore=ps.idsettore AND '.
					'pt.idsocieta=ps.idsocieta AND ps.scadenza = pt.scadenza)',
				"pt.idsocieta='$idsoc' AND pt.data_pagamento IS NULL AND pt.idtipo IS NOT NULL",
				'pt.*');
		$tipi = ModelFactory::listaSql('Pagamento', $rs);
		
		return $sett + $tipi;
	}
	
	/**
	 * Restituisce i pagamenti attivi per i tipi
	 * @param int $idtesserato
	 * @param int $idtipo
	 * @return Pagamento[] formato anno scadenza => Pagamento
	 */
	public function getCorrentiTipo($idtesserato, $idtipo) {
		$db = Database::get();
		
		$rq = $db->select('pagamenti_correnti',"idtesserato='$idtesserato' AND idtipo='$idtipo'");
		
		$res = array();
		while($row = $rq->fetch_assoc()) {
			$p = Modello::_creaConDati('Pagamento', $row);
			$res[$p->getDataScadenza()->getAnno()] = $p;
		}
		
		return $res;
	}
	
	/**
	 * Restituisce tutti i pagamenti correnti di una società
	 * @param integer $idsocieta l'id della società
	 * @return Pagamento[]
	 */
	public function inSocieta($idsocieta)
	{
		$db = Database::get();
		
		$rq = $db->select('pagamenti_correnti',"idsocieta='$idsocieta'");
		
		return ModelFactory::listaSql('Pagamento', $rq);
	}
	
	public function getSettoriPagati($idsocieta) {
		$db = Database::get();
		$rq = $db->select('pagamenti_correnti',"idsocieta='$idsocieta' AND idsettore IS NOT NULL AND data_pagamento IS NOT NULL ");
		return ModelFactory::listaSql('Pagamento', $rq);
	}
	
	/**
	 * Restituisce i pagamenti pagati da una società nell'anno $anno
	 * @param int $id_soc
	 * @param int $anno
	 * @return Pagamento[]
	 */
	public function getPagati($id_soc, $anno)
	{
		$db = Database::get();
		$rs = $db->select('pagamenti_correnti',"idsocieta='$id_soc' AND scadenza='$anno-12-31' AND data_pagamento IS NOT NULL");
		return ModelFactory::listaSql('Pagamento', $rs);
	}
	
	/**
	 * Restituisce i pagamenti non pagati da una società nell'anno $anno
	 * @param int $id_soc
	 * @param int $anno
	 * @return Pagamento[]
	 */
	public function getNonPagati($id_soc, $anno)
	{
		$db = Database::get();
		$rs = $db->select('pagamenti_correnti',"idsocieta='$id_soc' AND scadenza='$anno-12-31' AND data_pagamento IS NULL");
		return ModelFactory::listaSql('Pagamento', $rs);
	}
}