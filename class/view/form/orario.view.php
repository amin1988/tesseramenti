<?php
if (!defined('_BASE_DIR_')) exit();
include_formview(FORMVIEW_NUM);

class FormView_orario extends FormView_numero {
	
	function stampa($attr) {
		if (!isset($attr['pattern'])) {
			$p = '(2[0-3]|[01][0-9])\s*:\s*[0-5][0-9]';
			if (!$this->elem->isObbligatorio())
				$p = "($p)?";
			$attr['pattern'] = "\s*$p\s*";
		}
		if (!isset($attr['title'])) 
			$attr['title'] = 'hh:mm';
		if (!isset($attr['placeholder']))
			$attr['placeholder'] = 'hh:mm';
		parent::stampa($attr);
	}
	
	protected function defToString() {
		/* @var $ts Timestamp */
		$ts = $this->elem->getDefault();
		if ($ts === NULL) return '';
		else return $ts->format('H:i');
	}
}