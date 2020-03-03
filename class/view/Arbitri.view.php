<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('Arbitri');
include_model('Societa');
include_formview('FormView');

class Arbitri extends ViewWithForm {
	
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new ArbitriCtrl();
	}
	
	public function stampa()
	{
		
	}
	
}