<?php
session_start();
require_once 'config.inc.php';
include_view('ModificaConsiglio');

function salvaCons() {
	redirect("segr/soc/dati.php?soc=$_GET[soc]");
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_DATI);
$tmpl->setTitolo('Modifica consiglio');
$ids = $_GET['soc'];
$tmpl->addBody(new ModificaConsiglio($ids, 'salvaCons'));
$tmpl->stampa();