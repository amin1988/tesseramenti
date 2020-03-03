<?php
if (!defined('_BASE_DIR_')) exit();
include_controller('Login');
include_formview('FormView');

class Login extends ViewWithForm {
	private $ctrl;
	private $bordo;
	
	function __construct($bordo=true) {
		$this->ctrl = new LoginCtrl();
		$this->form = new FormView($this->ctrl->getForm());
		$this->bordo = false&&$bordo; //TODO far funzionare il bordo o toglierlo del tutto
	}
	
	public function stampa() {
		$f = $this->form;
		
		if ($this->bordo) $formClass = array('class'=>'login-border');
		else $formClass = NULL;
		$f->stampaInizioForm($formClass);
?>
<div class="row-fluid">
	<div class="span5 tab-label" >Username</div>
	<div class="span7"><?php $f->stampa(FORM_USER, NULL, array('class'=>'input-block-level')); ?></div>
</div>
<div class="row-fluid"> 
	<div class="span5 tab-label" >Password</div>
	<div class="span7"><?php $f->stampa(FORM_PSW, NULL, array('class'=>'input-block-level')); ?></div>
</div>
<div class="row-fluid">
	<div class="offset5 span7">
		<p class="text-error">
			<?php if ($this->ctrl->getErrore()){ echo 'Username / password errati';	}?>
		</p>
	</div>
</div>
<div class="row-fluid">
	<div class="offset5 span7">
		<?php $f->stampaSubmit(array('class'=>'btn-large btn-primary')); ?>
	</div>
</div>
<?php 
		$f->stampaFineForm();
	} //function stampa()
}
