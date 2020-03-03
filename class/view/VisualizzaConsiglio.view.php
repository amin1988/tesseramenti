<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('Consiglio');
include_model('Tesserato','Consiglio');

class VisualizzaConsiglio {
	private $ctrl;
	
	function __construct($idsoc) {
		$this->ctrl = new ConsiglioCtrl($idsoc);
	}
	
	function stampa() {
		/* @var $cons Consiglio */
		$cons = $this->ctrl->getConsiglio();
		$ruoli = Consiglio::getRuoli();
		
		foreach ($ruoli as $ruolo)
		{
			/* @var $membro Tesserato */
			$membro = $cons->getMembro($ruolo);
			
			if($membro !== NULL)
			{
				$rf = $this->ctrl->getNomeRuolo($ruolo);
				$id = $membro->getId();
				$nome = $membro->getNome();
				$cognome = $membro->getCognome();
				echo '<div class="row-fluid">';
				echo "<div class=\"span4 tab-label\">$rf:</div>\n";
				echo " <div class=\"span8\"><a href=\"tess.php?id=$id\" target=\"_blank\">$cognome $nome</a></div>";
				echo '</div>';
			}
		}
	}
}