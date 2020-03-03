<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Utente');

class UtenteMask extends Utente {
	private $idsoc;
	private $tipo;
	
	public function __construct($id, $idsoc=-1) {
		parent::__construct($id);
		
		if ($this->getTipoReale() == UTENTE_SOC)
			$idsoc = parent::getIDSocieta();
		elseif($idsoc < 0)
			$idsoc = NULL;
		$this->idsoc = $idsoc;
		
		if ($this->idsoc === NULL)
			$this->tipo = UTENTE_SEGR;
		else
			$this->tipo = UTENTE_SOC;
	}
	
	function getTipo() {
		return $this->tipo;
	}
	
	function getIDSocieta() {
		return $this->idsoc;
	}
}