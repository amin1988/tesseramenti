<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_checkbox extends FormView_Elem {
	function stampa($attr) {
		$nome = $this->elem->getNomeKey();
		echo '<input type="checkbox" ';
		if ($nome !== NULL)
			echo "name=\"$nome\" id=\"form_$nome\" ";
		if ($this->elem->getDefault() === true) 
			echo 'checked="checked" ';
		if ($this->elem->isDisabilitato() === true) 
			echo 'disabled="disabled" ';
		FormView::stampaAttr($attr);
		echo "value=\"1\" />\n";
	}
}