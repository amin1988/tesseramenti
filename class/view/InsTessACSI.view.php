<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('InsTessACSI');
include_formview('FormView');

class InsTessACSI extends ViewWithForm {
	
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new InsTessACSICtrl();
		$this->form = new FormView($this->ctrl->getForm());
	}
	
	public function stampa()
	{
		$fv = $this->form;
		$fv->stampaInizioForm();
		?>
		<div class=form-horizontal>
			<div class="control-group">
	    		<label class="control-label" for="form_<?php echo InsTessACSICtrl::NUM_DA; ?>">Numero tessera da:</label>
	   			<div class="controls"><?php $fv->stampa(InsTessACSICtrl::NUM_DA); ?>(numero incluso)</div>
			</div>
			<div class="control-group">
	    		<label class="control-label" for="form_<?php echo InsTessACSICtrl::NUM_A; ?>">Numero tessera a:</label>
	   			<div class="controls"><?php $fv->stampa(InsTessACSICtrl::NUM_A); ?>(numero incluso)</div>
			</div>
			<div class="control-group">
	    		<label class="control-label" for="form_<?php echo InsTessACSICtrl::ANNO; ?>">Anno validità tessere</label>
	   			<div class="controls"><?php $fv->stampa(InsTessACSICtrl::ANNO); ?></div>
			</div>
			<div class="control-group">
		   		<div class="controls"><?php $fv->stampa(InsTessACSICtrl::AGGIUNGI, NULL, array("class"=>"btn btn-primary")); ?></div>
			</div>
		</div>
		<?php
	}
}