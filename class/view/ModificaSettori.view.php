<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('ModificaSettori');
include_model('Settore');
include_formview('FormView');

class ModificaSettori extends ViewWithForm {
	private $ctrl;
	private $pulsanti;
	private $fed;
	
	public function stampaCss() {
		echo ".col-prezzo { padding-left:20px; text-align: right; }\n\n";
	}
	
	/**
	 * @param integer $idsocieta
	 * @param bool $rinnovo [def:false] true per considerare solo i settori rinnovati 
	 * @param callable $callback funzione da chiamare se la modifica va a buon fine
	 * @param boolean $pulsanti [def:true] true per stampare i pulsanti e chiudere il form
	 */
	function __construct($id_soc, $rinnovo=false, $callback=NULL, $pulsanti=true) {
		$this->ctrl = new ModificaSettoriCtrl($id_soc, $rinnovo, $callback);
		if ($pulsanti) $this->ctrl->getForm()->addSubmit('Modifica');
		$this->form = new FormView($this->ctrl->getForm());
		$this->pulsanti = $pulsanti;
		$this->fed = $this->ctrl->getFederazione();
	}
	
	function stampa() {
		$fv = $this->form;
	
		$fv->stampaInizioForm();
		
		echo '<table><tbody>';
		foreach(Settore::elenco() as $idsett=>$sett)
		{
			echo "<tr class=\"sett_$idsett\"><td>";
			$fv->stampa(ModificaSettoriCtrl::F_SETT, $idsett);
			/* @var $sett Settore */
			echo $sett->getNome();
			echo '</td><td class="col-prezzo">';
			printf("&euro; %.2f</td></tr>\n", $sett->getPrezzoEuro());
		}
		echo "</tbody></table>\n";
		
		if ($this->pulsanti) {
			$fv->stampaSubmit();
			$fv->stampaFineForm();
		}
	}
	
	public function stampaJsOnload() {
		
		if($this->fed == 2)
			echo "$(\".sett_1\").hide();";
		else 
			echo "$(\".sett_4\").hide();";
	}
}