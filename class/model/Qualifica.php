<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Pagamento');
include_class('Database');


class Qualifica {
	
	const TAB = 'tipi_tesserati';
	
	/**
	 * 
	 * @var Tesserato
	 */
	private $tesserato = NULL;
	private $idtipo = NULL;
	private $idgrado = NULL;
	private $_extra = NULL;
	private $_extraCaricati = false;
	
	private $esiste = NULL;	
	private $_errore = NULL; 
	
	/**
	 * Restituisce le qualifiche di un tesserato per le quali risulta un pagamento non scaduto
	 * @param Tesserato $tesserato
	 * @param bool $anni [def:false] true per separare le qualifiche per anno
	 * @return Qualifica[] o Qualifica[][] se anno == true formato: {anno} => idtipo => Qualifica
	 */
	 public static function getListaAttive($tesserato, $anni=false) {
	 	$idtesserato = $tesserato->getId();
		$rq = Database::get()->select("tipi_tesserati t INNER JOIN pagamenti_correnti p ".
				"ON (t.idtesserato=p.idtesserato AND t.idtipo=p.idtipo)","p.idtesserato='$idtesserato'",'t.*, YEAR(scadenza) as anno_scad');
		
		$res = array();
		while($row = $rq->fetch_assoc()) {
			/* @var $o Qualifica */
			$scad = $row['anno_scad'];
			unset($row['anno_scad']);
			$o = new Qualifica($tesserato, $row['idtipo'], $row['idgrado']);
			if ($anni) 
				$res[$scad][$o->getIdTipo()] = $o;
			else
				$res[$o->getIdTipo()] = $o;
		}
		return $res;
	}
	
	/**
	 * Restituisce le ultime qualifiche salvate sul database
	 * @param Tesserato $tesserato
	 * @return Qualifica[] formato id tipo => Qualifica
	 */
	public static function getListaUltime($tesserato) {
		$idtesserato = $tesserato->getId();
		$rq = Database::get()->select('tipi_tesserati',"idtesserato='$idtesserato'");
		
		$res = array();
		while($row = $rq->fetch_assoc()) {
			/* @var $o Qualifica */
			$o = new Qualifica($tesserato, $row['idtipo'], $row['idgrado']);
			$res[$o->getIdTipo()] = $o;
		}
		return $res;
	} 

	public function __construct($tesserato, $idtipo, $idgrado = NULL) {
		$this->tesserato = $tesserato;
		$this->idtipo = Database::get()->quote($idtipo);
		$this->idgrado = Database::get()->quote($idgrado);		
	}	
	
	/**
	 * Indica se questo oggetto è presente sul database
	 * @return boolean
	 */
	public function esiste() {
		if ($this->esiste === NULL) $this->carica();
		return $this->esiste;
	}
	
	/**
	 * Restituisce le chiavi di questa qualifica
	 * o NULL se è una nuova qualifica mai salvata sul database
	 * @return mixed|NULL
	 */
	public function getId() {
		$chiavi = array('idtesserato'=>$this->tesserato->getId(), 'idtipo'=>$this->idtipo);
		
		return $chiavi;
	}
	
	/**
	 * Salva l'oggetto sul database
	 */
	public function salva() {
		$this->_errore = NULL;
		if(!$this->tesserato->haId()) {
			$this->_errore = "Tesserato non ha id";
			return false;
		}
		$idtesserato = $this->tesserato->getId();
		$db = Database::get();
		$rq = $db->select(self::TAB, "idtesserato='$idtesserato' AND idtipo='$this->idtipo'");
		$row = $rq->fetch_assoc();
		
		//TODO tutta la gestione dei pagamenti andrebbe spostata in Pagamento
		
		if($row === NULL) //se nel database non c'è niente devo fare un insert e creare un pagamento
		{
			/* @var $p Pagamento */
			$p = Pagamento::creaQualifica($this->tesserato, Grado::fromId($this->idgrado));
			$rp = $p->salva();
			if(!$rp) {
				$this->_errore = "Pagamento: ".$p->getErrore(); 
				return false;
			}
			$rq = $db->insert(self::TAB, array('idtesserato'=>$idtesserato,'idtipo'=>$this->idtipo,'idgrado'=>$this->idgrado));
			
			if($rq) 
			{
				$this->esiste = true;
				$this->salvaExtra();
				return true;
			}
			else 
			{
				if ($rq)
					$this->_errore = "Nessuna riga inserita";
				else
					$this->_errore = "Insert: ".$db->error();
				$p->elimina();
				return false;
			}
		}
		else //se nel database c'è già una riga, la devo aggiornare se idgrado è stato modificato
		{
			$p = PagamentoUtil::get()->ultimoTipo($this->tesserato->getId(), $this->idtipo);
			if($p === NULL || (in_rinnovo() && !$p->isRinnovo()))
			{
				//non ci sono altri pagamenti correnti di questa qualifica
				//oppure siamo in periodo di rinnovo ma il pagamento è vecchio
				$oldp = $p;
				$p = Pagamento::creaQualifica($this->tesserato, Grado::fromId($this->idgrado));
				$rp = $p->salva();
				
				if(!$rp) {
					$this->_errore = "Pagamento: ".$p->getErrore();
					return false;
				} elseif ($oldp !== NULL && !$oldp->isPagato()) {
					//il vecchio pagamento non era pagato, va eliminato
					$oldp->elimina();
				}
			}
			
			$this->salvaExtra();
			if($row['idgrado']!=$this->idgrado)
			{
				//il grado è diverso da quello sul DB 
				if(!$p->isPagato())
				{
					//il pagamento esistente non è pagato, lo sostituiamo
					$pn = Pagamento::creaQualifica($this->tesserato, Grado::fromId($this->idgrado));
					$rp = $pn->salva();
					if($rp)
						$p->elimina();
				}
				
				//sta cambiando grado, inserire nello storico
				$rqtts = $db->insert('tipi_tesserati_storico', $row);
				if (!$rqtts)
					$this->_errore = 'Insert storico: '.$db->error();
				$rqtt = $db->update(self::TAB, array('idgrado'=>$this->idgrado),"idtesserato='$idtesserato' AND idtipo='$this->idtipo'");
				if (!$rqtt) {
					if ($this->_errore === NULL) $this->_errore = '';
					$this->_errore .= 'Update: '.$db->error();
				}
				
				return $rqtt&&$rqtts;
			}
			
			
			return true;
		}
	}
	
	/**
	 * Elimina l'oggetto dal database
	 */
	public function elimina() {
		if(!$this->tesserato->haId()) return true;
		$idtesserato = $this->tesserato->getId();
		$db = Database::get();
		
		$rq = $db->select(self::TAB, "idtesserato='$idtesserato' AND idtipo='$this->idtipo'");
		$row = $rq->fetch_assoc();
		
		if($row === NULL) return true;
		
		//se ci sono pagamenti pagati ancora validi allora non può eliminarlo
		$pc = PagamentoUtil::get()->getCorrentiTipo($idtesserato, $this->idtipo);
		$pagati = false;
		foreach ($pc as $anno=>$p) {
			if (!$p->isPagato()) {
				$p->elimina();
			} else {
				$pagati = true;
			}
		}
		//se ha un pagamento pagato non elimina la qualifica
		if ($pagati) return true;
		
		$rq = $db->insert('tipi_tesserati_storico', $row);
		$rq &= $db->affectedRows()==1;
		$rq &= $db->delete(self::TAB, "idtesserato='$idtesserato' AND idtipo='$this->idtipo'");
		$rq &= $p->elimina();

		$this->eliminaExtra();
		$this->esiste = false;
	
		return $rq;
	}
	
	public function ripristina() {//funzione di backup non gestita
		
	}
	

	public function getErrore() {
		return $this->_errore;
	}
	
	
	public function getIdTesserato() {
		return $this->tesserato->getId();
	}
	
	public function getIdTipo() {
		return $this->idtipo;
	}
	
	public function getIdGrado() {
		return $this->idgrado;
	}
		
	public function setIdGrado($idgrado) {
		$this->idgrado = $idgrado;
	}
	
	/**
	 * @return DatiExtra|NULL
	 */
	public function getDatiExtra() {
		if (!$this->_extraCaricati) {
			$idt = $this->getIdTipo();
			if ($idt === NULL) return NULL;
			include_model('Tipo');
			$this->_extra = Tipo::fromId($idt)->getDatiExtra($this);
			$this->_extraCaricati = true;
		}
		return $this->_extra;
	}
	
	public function getDatoExtra($key) {
		$e = $this->getDatiExtra();
		if ($e === NULL) return NULL;
		return $e->get($key);
	}
	
	public function setDatoExtra($key, $val) {
		$e = $this->getDatiExtra();
		if ($e === NULL) return;
		return $e->set($key, $val);
	}
	
	/**
	 * Carica i valori della qualifica dal database
	 */
	protected function carica() {
		$idtesserato = $this->tesserato->getId();
		$g = Database::get()->field(self::TAB,'idgrado', "idtesserato='$idtesserato' AND idtipo='$this->idtipo'");
		
		if($g !== NULL)
		{
			$this->idgrado = $g;
			$this->esiste = true;
		}
		else $this->esiste = false;
	}	
	
	/**
	 * Salva solo i dati extra, se presenti
	 * @return boolean
	 */
	public function salvaExtra() {
		if ($this->_extraCaricati && $this->_extra !== NULL)
			return $this->_extra->salva();
		return true;
	}
	
	protected function eliminaExtra() {
		$e = $this->getDatiExtra();
		if ($e !== NULL)
			$e->elimina();
	}
	

}