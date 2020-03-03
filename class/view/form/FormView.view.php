<?php
if (!defined("_BASE_DIR_")) exit();

define('FORMVIEW_INPUT','input');
define('FORMVIEW_DATE','data');
define('FORMVIEW_COMBO','combobox');
// define('FORMVIEW_CHECKLIST','checklist');
define('FORMVIEW_RADIOLIST','radiolist');
define('FORMVIEW_CHECK','checkbox');
define('FORMVIEW_STATIC','static');
define('FORMVIEW_AUTOLIST','autolista');
define('FORMVIEW_NUM','numero');
define('FORMVIEW_TEXTAREA','textarea');
define('FORMVIEW_TAB','tab');
define('FORMVIEW_TIME', 'orario');
define('FORMVIEW_TINYMCE','tinymce');

class FormView {
	/**
	 * @var Form
	 */
	private $form;
	private $tipi = array();
	private $elems = NULL;
	private $hidden = NULL;
	private $submit = NULL;
	

	public static function stampaAttr($attr) {
		if ($attr === NULL) return;
		foreach ($attr as $key => $val) {
			echo " $key=\"$val\"";
		}
		echo ' ';
	}
	
	/**
	 * @param Form $form
	 */
	public function __construct($form) {
		$this->form = $form;
	}
	
	/**
	 * Restituisce la form utilizzata da questa FormView
	 * @return Form
	 */
	public function getForm() {
		return $this->form;
	}
	
	/**
	 * Restituisce tutti gli elementi stampabili
	 * @return FormView_Elem[]
	 */
	public function getElems() {
		if ($this->elems === NULL) {
			$this->elems = array();
			$this->hidden = array();
			foreach ($this->form->getElems() as $el) {
				/* @var $el FormElem */
				$vel = $this->generaElem($el);
				$this->elems[$el->getNomeKey()] = $vel;
				if ($el->getTipo() == FORMELEM_HIDDEN)
					$this->hidden[] = $vel;
			}
			$el = $this->form->getSubmit();
			if ($el !== NULL && $el->getNome() === NULL) {
				$vel = $this->generaElem($el);
				$this->elems[] = $vel;
				$this->submit = $vel;
			} 
		}
		return $this->elems;
	}
	
	public function getSubview() {
		return $this->getElems();
	}
	
	/**
	 * Restituisce un elemento del FormView
	 * @param string $nome nome dell'elemento
	 * @param mixed $key [opz] chiave dell'elemento
	 * @return FormView_Elem o NULL se l'elemento non esiste
	 */
	public function getElem($nome, $key=NULL) {
		$elems = $this->getElems();
		$k = $nome . FormElem::keyToString($key);
		if (isset($elems[$k]))
			return $elems[$k];
		else
			return NULL;
	}
	
	/**
	 * Restituisce gli elementi nascosti
	 * @return FormView_Elem[]
	 */
	protected function getHiddenElems() {
		if ($this->hidden === NULL)
			$this->getElems();
		return $this->hidden;
	}
	
	/**
	 * Restituisce l'elemento corrispondente al submit, se esistente 
	 * @return FormView_Elem|NULL
	 */
	protected function getSubmit() {
		if ($this->elems === NULL)
			$this->getElems();
		return $this->submit;
	} 
	
	/**
	 * Crea un FormView_Elem per un FormElem
	 * @param FormElem $el
	 * @return FormView_Elem
	 */
	protected function generaElem($el) {
		$nk = $el->getNomeKey();
		if (isset($this->tipi[$nk])) {
			$tipo = $this->tipi[$nk];
		} elseif (isset($this->tipi[$el->getNome()])) {
			$tipo = $this->tipi[$el->getNome()];
		} else {
			$tipo = $this->getTipoDefault($el);
		}
		$class = "FormView_$tipo";
		if (!class_exists($class))
			include_formview($tipo);
		return new $class($el);
	} 
	
	/**
	 * Imposta il tipo di view da utilizzare per visualizzare l'elemento
	 * @param string $tipo una delle costanti FORMVIEW_* o un tipo personalizzato
	 * @param string $nome nome del componente
	 * @param mixed $key [opz] inserire la chiave per impostare il tipo solo per
	 * un elemento specifico 
	 */
	public function setTipo($tipo, $nome, $key=NULL) {
		$this->tipi[$nome . FormElem::keyToString($key)] = $tipo;
	} 
	
	/**
	 * Restituisce il tipo di default da utilizzare per visualizzare l'elemento
	 * @param FormElem $elem l'elemento da visualizzare
	 * @return string il tipo di view da utilizzare
	 */
	protected function getTipoDefault($elem) {
		switch ($elem->getTipo()) {
			case FORMELEM_DATE :
				return FORMVIEW_DATE;
			case FORMELEM_LIST :
				return FORMVIEW_COMBO;
			case FORMELEM_CHECK :
				return FORMVIEW_CHECK;
			case FORMELEM_STATIC :
				return FORMVIEW_STATIC;
			case FORMELEM_AUTOLIST :
				return FORMVIEW_AUTOLIST;
			case FORMELEM_NUM :
				return FORMVIEW_NUM;
			case FORMELEM_TIME :
				return FORMVIEW_TIME;
// 			case FORMELEM_ :
// 				return FORMVIEW_;
				default:
				return FORMVIEW_INPUT;
		}
	}
	
	/**
	 * Stampa il tag di apertura del form
	 * @param array $attr [opz] lista di attributi da aggiungere al tag form.
	 * Es. per aggiungere style="color:red;" passare array("style"=>"color:red;");
	 * @param boolean $hidden [def:true] true per stampare 
	 * tutti gli elementi di tipo FORMELEM_HIDDEN
	 */
	public function stampaInizioForm($attr=NULL, $hidden = true) {
		$f = $this->form;
		$nome = $f->getNome();
		if ($f->usaPost()) $method = 'POST';
		else $method = 'GET';
		
		echo "<form id=\"$nome\" method=\"$method\" enctype=\"multipart/form-data\" ";
		self::stampaAttr($attr);
		echo ">\n";
		if ($hidden) {
			foreach ($this->getHiddenElems() as $el) {
				$el->stampa(NULL);
			}
		}
	}
	
	/**
	 * Stampa il tag di fine form
	 */
	public function stampaFineForm() {
		echo '</form>';
	}
	
	public function stampaSubmit($attr=NULL) {
		$e = $this->getSubmit();
		if ($e === NULL) return;
		$e->stampa($attr);
	}
	
	/**
	 * Stampa un elemento del form
	 * @param string $nome nome dell'elemento
	 * @param mixed $key [opz] chiave dell'elemento
	 * @param array $attr [opz] lista di attributi da aggiungere all'elemento.
	 * Es. per aggiungere style="color:red;" passare array("style"=>"color:red;");
	 */
	public function stampa($nome, $key=NULL, $attr=NULL) {
		$elem = $this->getElem($nome, $key);
		if ($elem !== NULL)
			$elem->stampa($attr);
	}
	
	/**
	 * Indica se un elemento ha un errore
	 * @param string $nome
	 * @param mixed $key
	 * @return boolean
	 */
	public function isErrato($nome, $key=NULL) {
		if (!$this->form->isInviato()) return false;
		$elem = $this->getElem($nome, $key);
		if ($elem === NULL) return false;
		return $elem->getFormElem()->isErrato();
	}
	
}

class ViewWithForm {
	/**
	 * @var FormView
	 */
	protected $form;
	
	public function getForm() {
		return $this->form;
	}
	
	public function getSubview() {
		return $this->form;
	}
}

abstract class FormView_Elem {
	/**
	 * @var FormElem
	 */
	protected $elem;
	
	/**
	 * @param FormElem $elem
	 */
	public function __construct($elem) {
		$this->elem = $elem;
	}
	/**
	 * @return FormElem
	 */
	public function getFormElem() {
		return $this->elem;
	}
// 	public function getCssInclude() {}
// 	public function stampaCss() {}
// 	public function getJsInclude() {}
// 	public function stampaJsOnload() {}
// 	public function stampaJs() {}
	public abstract function stampa($attr);
}

class FormView_input extends FormView_Elem {
	
	function stampa($attr) {
		if ($attr === NULL || !isset($attr['type']))
			$attr['type'] = $this->elem->getTipo();
		$nome = $this->elem->getNomeKey();
		$val = $this->elem->getDefault();
// 		if ($attr === NULL || !isset($attr['class']))
// 			$attr['class'] = '';
// 		$attr['class'] .= 'form-control'; 
		if ($attr['type'] == 'submit') {//TODO gestire meglio
			if (!isset($attr['class']))
				$attr['class'] = '';
			$attr['class'] .= ' btn';
		}
		echo "<input ";
		if ($nome !== NULL)
			echo "name=\"$nome\" id=\"form_$nome\" ";
		if ($this->elem->isDisabilitato())
			echo 'disabled="disabled" ';
		if ($this->elem->isObbligatorio())
			echo 'required="required" ';
		FormView::stampaAttr($attr);
		echo "value=\"$val\" />\n";
	}
}
