<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('ModificaTesserato');
include_model('Tesserato');
include_view('FormViewTesserato');

class ModificaTesserato extends FormViewTesserato {
	
	/**
	 * @param integer $id_tes l'id del tesserato
	 * @param calable $callback [opz]
	 */
	function __construct($id_tes, $callback=NULL, $completo=false) {
		parent::__construct(new ModificaTesseratoCtrl($id_tes, $callback, $completo), true);
	}
	
	protected function stampaPulsanti($fv) {
		echo '<div class="control-group"><div class="controls">';
		$fv->stampa(ModificaTesseratoCtrl::SUBMIT_MOD, NULL, array('class'=>'btn-primary'));
	 	//$fv->stampa(ModificaTesseratoCtrl::SUBMIT_ELM, NULL, array('class'=>'btn-danger'));
	 	echo "<a class='btn' href='javascript:history.back()'>Annulla</a></div></div>\n";
	 	//TODO pulsante annulla configurabile
	}
}