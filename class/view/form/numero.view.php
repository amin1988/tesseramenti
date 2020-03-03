<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_numero extends FormView_Elem {
	
	function stampa($attr) {
		$nome = $this->elem->getNomeKey();
		$val = $this->defToString();
		if ($attr === NULL || !isset($attr['type']))
			$attr['type'] = 'text';
		echo '<input ';
		
		/* @var $el FormElem_Num */
		$el = $this->elem;
		if (!isset($attr['pattern']))
			$attr['pattern'] = $el->getRegex();
		if (!isset($attr['title'])) {
			$t = 'Solo numeri';
			if (!$el->accettaDecimali())
				$t .= ' interi';
			if (!$el->isNegValido())
				$t .= ' positivi';
			if (!$el->isZeroValido())
				$t .= ' diversi da zero';
			$attr['title'] = $t;
		}
		
		if ($nome !== NULL)
			echo "name=\"$nome\" id=\"form_$nome\" ";
		if ($this->elem->isDisabilitato() === true)
			echo 'disabled="disabled" ';
		if ($this->elem->isObbligatorio())
			echo 'required="required" ';
		FormView::stampaAttr($attr);
		echo "value=\"$val\" />\n";
	}
	
	protected function defToString() {
		return $this->elem->getDefault();
	}
}