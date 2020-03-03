<?php
if (!defined('_BASE_DIR_')) exit();
include_controller('ModificaCrediti');
include_formview('FormView');
include_model('Settore');

class ModificaCrediti extends ViewWithForm {
	private $ctrl;
	private $idsett;
	
	/**
	 * @param integer $id_tes l'id del tesserato
	 * @param calable $callback [opz]
	 */
	function __construct($idtess, $idsett, $callback=NULL) {
		$this->ctrl = new ModificaCreditiCtrl($idtess, $idsett);
		$this->form = new FormView($this->ctrl->getForm());
		$this->idsett = $idsett;
	}
	
	public function stampaJs() {
		echo "function confermaElimina() {\n";
		echo '  var risp = confirm("Eliminare i crediti selezionati?");';
		echo "\n  if (risp) $('form *:not([type=submit])').filter(':input:not(:button)').attr('disabled','disabled');";
		echo "\n  return risp;";
		echo "\n}\n\n";
	}
	
	public function stampa() {
		$fv = $this->form;
		
		$cogn = $this->ctrl->getTesserato()->getCognome();
		$nome = $this->ctrl->getTesserato()->getNome();
		$sett = Settore::fromId($this->idsett)->getNome();
		echo "<h4>$cogn $nome - settore $sett</h4>";
		
		$fv->stampaInizioForm();
		if ($this->ctrl->haEliminato()) {
			echo '<div class="alert alert-success">Crediti eliminati correttamente</div>';
		}elseif ($this->ctrl->haSalvato()) {
			echo '<div class="alert alert-success">Crediti inseriti correttamente</div>';
		}
		
		echo '<table class="table table-striped table-hover">';
		echo "\n<thead><tr><td>Data</td><td>Descrizione</td><td>Crediti</td><td></td></tr></thead>\n";
		echo '<tbody><tr><td>';
		$fv->stampa(ModificaCreditiCtrl::F_DATA, NULL, array(FormView_data::ATTR_HINT=>NULL));
		echo '</td><td>';
		$fv->stampa(ModificaCreditiCtrl::F_DESC);
		echo '</td><td>';
		$fv->stampa(ModificaCreditiCtrl::F_CREDITI);
		echo '</td><td>';
		$fv->stampa(ModificaCreditiCtrl::SUBMIT_ADD, NULL, array('class'=>'btn-primary'));
		echo "</td></tr>\n";
		foreach ($this->ctrl->getCrediti() as $c) {
			$idc = $c->getId();
			$data = $c->getDataAss()->toDMY();
			$desc = $c->getDescrizione();
			$num = $c->getCrediti();
			if ($this->ctrl->isCreditoSalvato($idc))
				echo '<tr class="success">';
			else
				echo '<tr>';
			echo "<td>$data</td><td>$desc</td><td>$num</td><td>"; 
			$fv->stampa(ModificaCreditiCtrl::SUBMIT_DEL, $idc, array('class'=>'btn-danger','onclick'=>'return confermaElimina();'));
			echo "</td></tr>\n";
		}
		echo "</tbody></table>";
		$fv->stampaFineForm();
	}
	
}