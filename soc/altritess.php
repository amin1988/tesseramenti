<?php
session_start();
require_once 'config.inc.php';
include_view('RinnovaTesserati');

function url_fase($fase) {
	return "soc/altritess.php?fase=$fase";
}

function url_finish() {
	return "soc/altritess.php";
}

if (isset($_GET['fase']))
	$fase = $_GET['fase'];
else
	$fase = 1;

set_time_limit(300);

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_TESS);
$tmpl->setTitolo('Altri tesserati');

$ids = Auth::getUtente()->getIDSocieta();
$tmpl->addBody(new RinnovaTesserati($ids, false, 'url_fase', true, $fase, 'url_finish'));
$tmpl->stampa();
