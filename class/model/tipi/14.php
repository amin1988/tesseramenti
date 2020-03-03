<?php

if (!defined('_BASE_DIR_')) exit();

include_model('DatiDan','KarateUtil');



class TipoSpec_KarateAtletaKudo extends TipoSpec {

	

	

	function getGradiModificabili($idgrado) {

		//può modificare solo fino a blu (2° kyu) e solo aumentando

		$kyu = KarateUtil::get()->gradoToKyu($idgrado);

		if ($kyu === NULL || $kyu < 2) return NULL;

		

		//prende tutti i kyu dall'attuale a marrone inclusi

		$kyu_list = array();

		for ($k = 1; $k <= $kyu; $k++)

			$kyu_list[] = $k;

		

		$res = KarateUtil::get()->listaKyuToGradi($kyu_list);

		if (count($res) < 2) 

			return NULL;

		else

			return $res;

	}	

	

	function getDatiExtra($qualifica) {

		return DatiDan::fromId($qualifica);

	}

}



//aggiunge il tipospec al registro

TipoSpecUtil::get()->_addTipoSpec(14, 'TipoSpec_KarateAtletaKudo');