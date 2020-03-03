<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_tab extends FormView_input {
	
	public static function getTabId($nome) {
		return "tab_$nome";
	}
	
	public static function getPaneId($nome, $id) {
		return "tab-pane_{$nome}_$id";
	}
	
	public function stampaJsOnload() {
		$nome = $this->elem->getNomeKey();
		$tabid = self::getTabId($nome);
		//cambio tab
		echo "$('#$tabid a').click(function (e) {\n";
		echo "  e.preventDefault();\n  var th = $(this);\n";
		echo "  $(document.getElementById('form_$nome')).val(th.data('val'));\n";
		echo "  th.tab('show');\n});\n";
		//seleziona tab
		$paneid = self::getPaneId($nome, $this->elem->getDefault());
		echo "$('#$tabid a[href=\"#$paneid\"]').tab('show');\n";
	}
	
	function stampa($attr) {
		$attr['type'] = 'hidden';
		parent::stampa($attr);
		
		$nome = $this->elem->getNomeKey();
		$def = $this->elem->getDefault();
		$tabid = self::getTabId($nome);
		echo "<ul class=\"nav nav-tabs\" id=\"$tabid\">\n";
		if (!$this->elem->isObbligatorio() || $this->elem->getDefault(false) === NULL) {
			$this->stampaTab($nome, '', 'Nessuno');
		}
		foreach ($this->elem->getLista() as $id => $val) {
			$this->stampaTab($nome, $id, $this->elem->valToString($val));
		}
		echo "</ul>\n";
	}
	
	private function stampaTab($nome, $id, $str) {
		$paneid = self::getPaneId($nome, $id);
		echo "<li><a href=\"#$paneid\" data-val=\"$id\">";
		echo "$str</a></li>\n";
	}
	
}