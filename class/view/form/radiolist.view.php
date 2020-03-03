<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_radiolist extends FormView_Elem {

	function stampa($attr) {
		if ($attr !== NULL && isset($attr['option'])) {
			$opt = $attr['option'];
			unset($attr['option']);
			$this->stampaSingolo($attr, $opt);
		} else
			$this->stampaGruppo($attr);		
	}
	
	private function stampaRadio($attr, $idopt, $def) {
		$nome = $this->elem->getNomeKey();
		echo '<input type="radio" ';
		if ($nome !== NULL)
			echo "name=\"$nome\" id=\"form_{$nome}_{$idopt}\" ";
		if ($def !== NULL && $def == $idopt)
			$attr['checked'] = 'checked';
		if ($this->elem->isDisabilitato())
			$attr['disabled'] = 'disabled';
		if ($this->elem->isObbligatorio())
			$attr['required'] = 'required';
		FormView::stampaAttr($attr);
		echo "value=\"{$idopt}\" />";
	}
	
	private function stampaSingolo($attr, $idopt) {
		$opt = $this->elem->getElemLista($idopt);
	 	if ($opt === NULL) return;
	 	
		
		$lbl = (isset($attr['label']) && $attr['label']);
		unset($attr['label']);
		if ($lbl) echo '<label>';
		$this->stampaRadio($attr, $idopt, $this->elem->getDefault());
		if ($lbl) {
			echo $this->elem->valToString($opt);
			echo '</label>';
		}
		echo "\n";
	 }
	 
	 private function stampaGruppo($attr) {
	 	$el = $this->elem;
	 	/* @val $el FormElem_List */
	 	$sel = $el->getDefault();
	 	echo '<div class="form-inline">';
	 	foreach ($el->getLista() as $id=>$val) {
	 		echo '<label class="radio">';
	 		$this->stampaRadio($attr, $id, $sel);
	 		echo $el->valToString($val);
	 		echo "</label>\n";
	 	}
	 	echo "</div>\n";
	 }
}
