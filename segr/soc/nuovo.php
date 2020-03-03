<?php
session_start();
require_once 'config.inc.php';
include_view('NuovoTesserato');

/**
 * @param Tesserato $tess
 */
function salvataggio_ok($tess) {
	redirect('segr/soc/tess.php?id='.$tess->getId());
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_TESS);
$tmpl->setTitolo('Nuovo tesserato');

$ids = $_GET['soc'];
$tmpl->addBody(new NuovoTesserato($ids, 'salvataggio_ok'));
$tmpl->stampa();
