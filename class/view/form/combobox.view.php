<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_combobox extends FormView_Elem {
	function stampa($attr) {
		$nome = $this->elem->getNomeKey();
		echo "<select name=\"$nome\" id=\"form_$nome\"";
		if ($this->elem->isDisabilitato())
			echo ' disabled="disabled" ';
		if ($this->elem->isObbligatorio())
			echo ' required="required" ';
		FormView::stampaAttr($attr);
		echo ">\n";
		$def = $this->elem->getDefault();
		//elemento vuoto?
		if (!$this->elem->isObbligatorio() || $this->elem->getDefault(false) === NULL) {
			echo '<option value=""></option>';
		}
		foreach ($this->elem->getLista() as $id => $val) {
			if (is_array($val)) {
				echo "<optgroup label=\"$id\">\n"; //TODO altro modo per label?
				foreach ($val as $id2 => $val2)
					$this->stampaOption($id2, $val2, $def);
				echo "</optgroup>\n";
			} else 
				$this->stampaOption($id, $val, $def);
		}
		
		echo "</select>\n";
	}
	
	private function stampaOption($id, $val, $def) {
		$str = $this->elem->valToString($val);
		echo "<option value=\"$id\" ";
		if ($def !== NULL && $def == $id) echo 'selected="selected" ';
		echo ">$str</option>\n";
	}
}