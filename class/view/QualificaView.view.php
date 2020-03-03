<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Grado');

define('_QUALVIEW_DIR_', _CLASS_DIR_.'view/qualifiche/');

class QualificaView {
	/**
	 * @var Qualifica
	 */
	protected $qual;
	/**
	 * @var int
	 */
	protected $idtipo;
	/**
	 * @var FormView
	 */
	protected $form;
	/**
	 * Nome dell'elemento di tipo FormElem_Grado
	 * @var string
	 */
	protected $nomeEl;
	
	/**
	 * Restituisce il nome del grado
	 * @param Qualifica $qualifica
	 * @return string
	 */
	public static function getNome($qualifica) {
		if ($qualifica === NULL) return '';
		return Grado::fromId($qualifica->getIdGrado())->getNome();
	}
	
	/**
	 * @param mixed $qualificaTipo Qualifica o idtipo
	 * @param Qualifica $qualifica obbligatorio se $idtipo == NULL
	 * @param int $idtipo nome dell'elemento di tipo grado
	 * @param string $nomeEl 
	 * @param string $formview
	 */
	public function __construct($qualifica, $idtipo, $nomeEl, $formview=NULL) {
		$this->qual = $qualifica;
		$this->idtipo = $idtipo;
		$this->form = $formview;
		$this->nomeEl = $nomeEl;
	}
	
	protected function isDisabilitato($idtesserato=NULL) {
		$el = $this->form->getElem($this->nomeEl, $this->getElemKey($idtesserato));
		return $el === NULL || $el->getFormElem()->isDisabilitato(); 
	}
	
	/**
	 * Stampa la view per la selezione del grado e di eventuali dati extra
	 * @param string $idtesserato [opz] id del tesserato che si sta stampando
	 */
	public function stampa($idtesserato = NULL) {
		if ($this->form === NULL) return;
		
		/* @var $el FormElem_Grado */
		$el = $this->form->getElem($this->nomeEl, $this->getElemKey($idtesserato));
		if ($el === NULL) return;
		
		if ($el->getFormElem()->isDisabilitato()) {
			echo QualificaViewUtil::get()->getNome($el->getFormElem()->getDefault(true));
		} else {
			$this->stampaInner($idtesserato);
		}
	}
	
	/**
	 * Stampa la view dopo aver verificato che la modifica è abilitata
	 * @param string $idtesserato [opz] id del tesserato che si sta stampando
	 * @see QualificaView::stampa
	 */
	protected function stampaInner($idtesserato) {
		$this->form->stampa(FormElem_Grado::getNomeGrado($this->nomeEl), $this->getElemKey($idtesserato));
	}
	
	protected function getElemKey($idtess) {
		if ($idtess === NULL)
			return $this->idtipo;
		else
			return array($idtess, $this->idtipo);
	}
}

class QualificaViewUtil {
	private static $inst = NULL;
	
	/**
	 * Registro delle classi QualificaView per ogni tipo
	 * @var string[]
	 */
	private $tab = array();
	
	/**
	 * @return QualificaViewUtil 
	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new QualificaViewUtil();
		return self::$inst;
	}
	
	protected function getClasse($idtipo) {
		if (!isset($this->tab[$idtipo])) {
			$file = _QUALVIEW_DIR_ . $idtipo . '.php';
			if (file_exists($file)){
				//include il file, che deve aggiungere la classe da solo
				require_once $file;
				if (!isset($this->tab[$idtipo])) {
					//il file non ha aggiunto la classe
					Log::warning('QualificaView non aggiunge classe', $idtipo);
					$this->_addClass($idtipo, 'QualificaView'); 
				}
			} else {
				//usa la classe di default
				$this->_addClass($idtipo, 'QualificaView');
			}
		}
		return $this->tab[$idtipo];
	}
	
	/**
	 * Restituisce l'oggetto QualificaView relativo ad una certa qualifica
	 * @param Qualifica|int $qualificaTipo qualifica o idtipo
	 * @param string $nomeEl nome dell'elemento di tipo grado
	 * @param string $formview [opz]
	 * @return QualificaView
	 */
	public function getView($qualificaTipo, $nomeEl, $formview=NULL) {
		if (is_object($qualificaTipo)) {
			$qualifica = $qualificaTipo;
			$idtipo = $qualifica->getIdTipo();
		} else {
			$qualifica = NULL;
			$idtipo = $qualificaTipo;
		}
		
		$classe = $this->getClasse($idtipo);
		return new $classe($qualifica, $idtipo, $nomeEl, $formview);
	}
	
	/**
	 * Restituisce un array di QualificaView corrispondenti 
	 * alle qualifiche in input
	 * @param Qualifica[] $ql
	 * @param string $nomeEl nome dell'elemento di tipo grado
	 * @return QualificaView[]
	 */
	public function getList($ql, $nomeEl) {
		$r = array();
		foreach($ql as $q) {
			$r[$q->getIdTipo()] = $this->getView($q, $nomeEl);
		}
		return $r;
	}
	
	/**
	 * Restituisce il nome del grado di una qualifica
	 * @param Qualifica $qualifica
	 */
	public function getNome($qualifica) {
		if ($qualifica === NULL || $qualifica->getIdTipo() === NULL)
			return '';
		
		$classe = $this->getClasse($qualifica->getIdTipo());
		$meth = array($classe, 'getNome');
		if (is_callable($meth)) {
			return call_user_func($meth, $qualifica);
		} else {
			return QualificaView::getNome($qualifica);
		}
	}
	
	/**
	 * Imposta la classe QualificaView da utilizzare per un certo tipo 
	 * @param int $idtipo ID del tipo
	 * @param string $classe nome della classe che estende QualificaView
	 */
	public function _addClass($idtipo, $classe) {
		$this->tab[$idtipo] = $classe;
	}
	
}
