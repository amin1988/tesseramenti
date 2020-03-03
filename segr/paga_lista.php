<?php
session_start();
require_once 'config.inc.php';
include_view('ListaSocieta');

/**
 * @param Societa $soc
 */
function paga_url($soc) {
	return 'paga.php?id='.$soc->getId();
}

if(isset($_GET['id_fed']))
	$id_fed = $_GET['id_fed'];
else 
	$id_fed = 1;

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_PAGA);
$tmpl->setTitolo('Saldo pagamenti');
$tmpl->addBody(new ListaSocieta('paga_url',$id_fed));
$tmpl->stampa();
