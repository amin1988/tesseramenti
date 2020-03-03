<?php
if (!defined('_BASE_DIR_')) exit();
include_model('DatiEtsiaAtl');

class TipoSpec_KarateETSIAAtleta extends TipoSpec {
	
	function getDatiExtra($qualifica) {
		//anche gli atleti hanno sempre il kyu/dan
		return DatiEtsiaAtl::fromId($qualifica);
	}
}

//aggiunge il tipospec al registro
TipoSpecUtil::get()->_addTipoSpec(7, 'TipoSpec_KarateETSIAAtleta');
