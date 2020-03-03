<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_autolista extends FormView_Elem {
	
	function getJsInclude() {
		return array('fv_autolista');
	}
	
	function stampaJsOnload() {
		/* @var $e FormElem_AutoList */
		$e = $this->elem;
		$src = 'form_'.$e->getSorgente()->getNomeKey();
		$dest = 'form_'.$e->getNomeKey();
		$ajax = $e->getAjax();
		echo "$(document.getElementById('$src')).change(function() {fv_autolista_autoload('$src','$dest','$ajax')});\n";
	}
	
	function stampa($attr) {
		$nome = $this->elem->getNomeKey();
		$def = $this->elem->getDefault();
		
		echo "<select name=\"$nome\" id=\"form_$nome\"";
		if ($def === NULL || $this->elem->isDisabilitato())
			echo ' disabled="disabled" ';
		if ($this->elem->isObbligatorio())
			echo ' required="required" ';
		FormView::stampaAttr($attr);
		echo ">\n";
		
		if ($def !== NULL) {
			echo '<option value=""></option>';
			foreach ($this->elem->getLista() as $id => $val) {
				$str = $this->elem->valToString($val);
				echo "<option value=\"$id\" ";
				if ($def == $id) echo 'selected="selected" ';
				echo ">$str</option>\n";
			}
		}

		echo "</select>\n";
	}
}