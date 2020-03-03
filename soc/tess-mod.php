<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('ModificaTesserato');

/**
 * @param Tesserato $tes
 */
function salvato($tes) {
	redirect('soc/tess.php?id='.$tes->getId());
}

$tmpl = get_template();
$tmpl->setTitolo('Modifica tesserato');
$tmpl->addBody(new ModificaTesserato($_GET['id'], 'salvato'));
$tmpl->stampa();
