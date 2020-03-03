<?php
session_start();
require_once 'config.inc.php';
include_view('ListaSocieta');

/**
 * @param Societa $soc
 */
function soc_url($soc) {
	return _PATH_ROOT_.'segr/soc/dati.php?soc='.$soc->getId();
}

if(isset($_GET['id_fed']))
	$id_fed = $_GET['id_fed'];
else
	$id_fed = 1;

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_SOC);
$tmpl->setTitolo('Societ&agrave;');
$listaSocieta = new ListaSocieta('soc_url',$id_fed,true);
$tmpl->addBody($listaSocieta);
$listaSocieta->setSettore(7);
$tmpl->stampa();