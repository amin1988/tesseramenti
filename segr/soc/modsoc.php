<?php
session_start();
require_once 'config.inc.php';
include_view('ModificaSocieta');

function url_dett($soc) {
	$id = $soc->getId();
	redirect("segr/soc/dati.php?soc=$id");
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_DATI);
$tmpl->setTitolo('Modifica societa');
$tmpl->addBody(new ModificaSocieta($_GET['soc'],true,'url_dett'));
$tmpl->stampa();
