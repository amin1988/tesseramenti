<?php
session_start();
require_once 'config.inc.php';
include_view('NuovoTesserato');

function salvataggio_ok($tes) {
	redirect('soc/work/datites.php?id='.$tes->getId());
}

$tmpl = get_template();
$tmpl->setTitolo('Nuovo tesserato');
$tmpl->addBody(new NuovoTesserato(Auth::getUtente()->getIDSocieta(), 'salvataggio_ok'));
$tmpl->stampa();

