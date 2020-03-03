<?php
if (!defined('_BASE_DIR_')) exit();
include_model('DatiEtsiaTec');

class TipoSpec_KarateETSIATecnico extends TipoSpec {
	
	function getDatiExtra($qualifica) {
		//i tecnici hanno sempre il dan
		return DatiEtsiaTec::fromId($qualifica);
	}
}

//aggiunge il tipospec al registro
TipoSpecUtil::get()->_addTipoSpec(8, 'TipoSpec_KarateETSIATecnico');
