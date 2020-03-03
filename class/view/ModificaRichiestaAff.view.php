<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('ModificaRichiestaAff');
include_model('RichiestaAff');
include_view('FormViewRichiestaAff');

class ModificaRichiestaAff extends FormViewRichiestaAff {

	public function stampaJs() {
		echo "function confermaRifiuta() {\n";
		echo '  var risp = confirm("Attenzione! Una volta rifiutata la richiesta verrà cancellata.\\nContinuare?");';
		echo "\n  if (risp) $('form *:not([type=submit])').filter(':input:not(:button)').attr('disabled','disabled');\n  return risp;";
		echo "\n}\n\n";
	}
	
	public function __construct($id_rich, $callback)
	{
		parent::__construct(new ModificaRichiestaAffCtrl($id_rich, $callback), true);
	}
	
	protected function stampaPulsanti($fv) {
		$fv->stampa(ModificaRichiestaAffCtrl::SUBMIT_ACCETTA, NULL, array('class'=>'btn-success'));
  		$fv->stampa(ModificaRichiestaAffCtrl::SUBMIT_RIFIUTA, NULL, array('class'=>'btn-danger','onclick'=>'return confermaRifiuta();'));
  		echo "<a class='btn' href='javascript:history.back()'>Annulla</a><br>";
	}
}