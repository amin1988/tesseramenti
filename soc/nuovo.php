<?php
session_start();
require_once 'config.inc.php';
include_view('NuovoTesserato');

/**
 * @param Tesserato $tess
 */
function salvataggio_ok($tess) {
	redirect('soc/tess.php?id='.$tess->getId());
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_TESS);
$tmpl->setTitolo('Nuovo tesserato');

$ids = Auth::getUtente()->getIDSocieta();
$tmpl->addBody(new NuovoTesserato($ids, 'salvataggio_ok'));
$tmpl->stampa();
