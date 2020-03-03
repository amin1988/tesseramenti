<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('DettagliTesserato');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_TESS);
$tmpl->setTitolo('Dettagli tesserato');
$tmpl->addBody(new DettagliTesserato($_GET['id'], Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();
