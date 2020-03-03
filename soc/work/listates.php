<?php
session_start();
require_once 'config.inc.php';
check_get('idt');
include_view('ListaTesserati');

/**
 * @param Tesserato $tes
 */
function url_dett($tes) {
	return  _PATH_ROOT_.'soc/work/datitess.php?id='.$tes->getId();
}

$tmpl = get_template();
$tmpl->setTitolo('Lista tesserati');
$tmpl->addBody(new ListaTesserati(Auth::getUtente()->getIDSocieta(),$_GET['idt'],'url_dett'));
$tmpl->stampa();
