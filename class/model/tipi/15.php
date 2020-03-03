<?php

if (!defined('_BASE_DIR_')) exit();

include_model('DatiDan');



class TipoSpec_KarateTecnicoKudo extends TipoSpec {

	

	function getDatiExtra($qualifica) {

		//i tecnici hanno sempre il dan

		return DatiDan::fromId($qualifica);

	}

}



//aggiunge il tipospec al registro

TipoSpecUtil::get()->_addTipoSpec(15, 'TipoSpec_KarateTecnicoKudo');