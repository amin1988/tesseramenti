<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('NuovoTesserato');
include_view('NuovoTesserato');
include_class('Sesso');
include_formview('FormView');

class NuovoTessPop extends NuovoTesserato
{
	
	public function __construct($idsocieta, $callback)
	{
		parent::__construct($idsocieta, $callback, false);
	}

	public function getCssInclude()
	{
		return array('nuovotesspop');
	}

	public function getJsInclude()
	{
		return array('nuovotess');
	}
	
	public function stampaJsOnload()
	{
		echo "$('#dialog-nuovo-tesserato').modal({ show: false, backdrop: 'static' });\n";
	}
	
	function stampa()
	{
		?>
		<div id="dialog-nuovo-tesserato" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Nuovo tesserato</h3>
			</div>
			<div class="modal-body">
				<div class="alert alert-info inviando">Invio in corso...</div>
				<div class="alert alert-error errore-invio">Errore durante l'invio dei dati, riprovare</div>
				<?php
				parent::stampa();
				$this->form->stampaFineForm();
				?>
			</div>
			<div class="modal-footer">
				<button type="button" id="invia-nuovo-tesserato" class="btn btn-primary" onclick="nuovoTesseratoPop.invia(); return false;">Nuovo</button>
				<button type="button" class="btn" data-dismiss="modal">Chiudi</button>
			</div>
		</div>
		<?php 
	}
}