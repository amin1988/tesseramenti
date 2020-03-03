<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('Comunicazione');
include_model('Societa');
include_formview('FormView');

class Comunicazione extends ViewWithForm {
	
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new ComunicazioneCtrl();
		$this->form = new FormView($this->ctrl->getForm());
		$this->form->setTipo(FORMVIEW_TEXTAREA, ComunicazioneCtrl::SUBJECT);
		$this->form->setTipo(FORMVIEW_TINYMCE, ComunicazioneCtrl::BODY);
	}
	
	public function stampa()
	{
		$fv = $this->form;
		$fv->stampaInizioForm();
		
		echo "<h4>Invia a:</h4>";
		
		echo "<div class=\"well\">";
		echo "<center><table><tr>";
		$i = 0;
		foreach ($this->ctrl->getSocieta() as $id_s=>$soc)
		{
			if($i % 5 === 0 && $i != 0)
				echo "</tr><tr>";
			?>
		<td>
			<div class="control-group">
				<div class="controls"><label class="checkbox"><?php $fv->stampa(ComunicazioneCtrl::EMAIL_CH, $id_s); echo $soc->getNomeBreve()?></label></div>
			</div>
		</td>
			<?php
			$i++;
		}
		echo "</tr></table></center></div>";
		?>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::SUBJECT; ?>"><h4>Oggetto:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::SUBJECT, NULL, array("class"=>"input-block-level")); ?></div>
		</div>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::BODY; ?>"><h4>Messaggio:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::BODY); ?></div>
		</div>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::ATTCH_1; ?>"><h4>Allegato:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::ATTCH_1); ?></div>
		</div>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::ATTCH_2; ?>"><h4>Allegato:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::ATTCH_2); ?></div>
		</div>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::ATTCH_3; ?>"><h4>Allegato:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::ATTCH_3); ?></div>
		</div>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::ATTCH_4; ?>"><h4>Allegato:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::ATTCH_4); ?></div>
		</div>
		<div class="control-group">
    		<label class="control-label" for="form_<?php echo ComunicazioneCtrl::ATTCH_5; ?>"><h4>Allegato:</h4></label>
   			<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::ATTCH_5); ?></div>
		</div>
		<div class="control-group">
		   		<div class="controls"><?php $fv->stampa(ComunicazioneCtrl::INVIA, NULL, array("class"=>"btn btn-primary")); ?></div>
		</div>
		<?php
		
		$fv->stampaFineForm();
	}
}