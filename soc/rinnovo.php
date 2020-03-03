<?php
session_start();
require_once 'config.inc.php';
include_model('Societa');

//attiva in periodo di rinnovo, o se una società ancora non ha terminato il rinnovo
$rinn = Societa::fromId(Auth::getUtente()->getIDSocieta())->isRinnovata();
$sett = isset($_GET['fase']);
if(!in_rinnovo()) 
	if($rinn && !$sett) go_home();

include_view('Rinnovo');

function url_fase($fase) {
	return "soc/rinnovo.php?fase=$fase";
}

if (isset($_GET['fase']))
	$fase = $_GET['fase'];
else
	$fase = NULL;
$view = new Rinnovo(Auth::getUtente()->getIDSocieta(), $fase, 'url_fase');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_RINNOVO);
$tmpl->setTitolo('Rinnovo');
// $tmpl->addBody($view, $view->getInnerView(), $view->getFooterView());
$tmpl->addBody($view);
$tmpl->stampa();
