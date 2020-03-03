<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_tinymce extends FormView_Elem { 
	
	function stampaJs()
	{
		$nome = $this->elem->getNomeKey();
		echo "tinymce.init({
		language:'it',
		selector: '#form_$nome',
		removed_menuitems: 'newdocument'});\n";
	}
	
	function getJsInclude()
	{
		return array("tinymce/tinymce.min");
	}
	
	function stampa($attr) {
		$nome = $this->elem->getNomeKey();
		$val = $this->elem->getDefault();
		echo "<textarea ";
		if ($nome !== NULL)
			echo "name=\"$nome\" id=\"form_$nome\" ";
		if ($this->elem->isDisabilitato())
			echo 'disabled="disabled" ';
		if ($this->elem->isObbligatorio())
			echo 'required="required" ';
		FormView::stampaAttr($attr);
		echo ">$val</textarea>\n";
	}
}