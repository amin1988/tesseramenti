<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('SaldaPagamenti');
include_formview('FormView');

class SaldaPagamenti extends ViewWithForm {
	private $ctrl;
	
	public function __construct($idsoc) {
		$this->ctrl = new SaldaPagamentiCtrl($idsoc);
		$this->ctrl->getForm()->addSubmit('Inserisci');
		$this->form = new FormView($this->ctrl->getForm()); 
	}
	
	public function stampa() {
		$fv = $this->form;
		$fv->stampaInizioForm(array('class'=>'form-horizontal'));
		$nome = SaldaPagamentiCtrl::F_PAGATO;
		echo '<div class="control-group"><label class="control-label" for="form_'.$nome.'">Pagamento ricevuto:</label>';
		echo '<div class="controls">';
		$fv->stampa($nome, NULL, array('placeholder'=>'0,00'));
		echo ' &euro;</div></div>';
		echo '<div class="control-group"><label class="control-label">Da pagare:</label>';
		$tot = str_replace('.', ',', sprintf('%.2f', $this->ctrl->getTotale()));
		echo '<div class="controls">'.$tot.' &euro;</div></div>';
		echo '<div class="form-actions"><a href="javascript:history.back()" class="btn">Indietro</a> ';
		$fv->stampaSubmit(array('class'=>'btn-primary'));
		echo '</div>';
		$fv->stampaFineForm();
	}
}
