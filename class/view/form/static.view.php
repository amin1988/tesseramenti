<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_static extends FormView_Elem {
	function stampa($attr) {
		echo $this->elem->getDefault();
	}
}