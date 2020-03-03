<?php
if (!defined("_BASE_DIR_")) exit();
include_view('FormViewRichiestaAff');
include_controller('NuovaRichestaAff');

class NuovaRichiestaAff extends FormViewRichiestaAff {
	public function __construct()
	{
		parent::__construct(new NuovaRichiestaAffCtrl(), false);
	}
	
	public function stampa() {
		
		if ($this->ctrl->isSalvato()) {
			echo '<div class="alert alert-success text-center"><h4>Richiesta inviata correttamente</h4></div>';
		} else {
			parent::stampa();
		}
	}
	
	protected function stampaPulsanti($fv) {
		$fv->stampaSubmit(array('class'=>'btn-primary'));
	}
	
	public function stampaJsOnload() {
		echo "$(\".sett_4\").hide(); \n\n";
		echo "$(\"#form_".FormAffiliazione::FEDER."\").change(function(){\n";
		echo <<<js
			var fed = $(this).val();
			if(fed == 2) { 
				$(".sett_1").hide(); 
				$(".sett_4").show();
			}
			else {
				$(".sett_1").show(); 
				$(".sett_4").hide();
			}
		});
js;
	}
	
}