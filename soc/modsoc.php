<?php
session_start();
require_once 'config.inc.php';
include_view('ModificaSocieta');

function url_dett($soc) {
	$id = $soc->getId();
	redirect("admin/datisoc.php?id=$id");
}

$tmpl = get_template();
$tmpl->setTitolo('Modifica societa');
$tmpl->addBody(new ModificaSocieta(Auth::getUtente()->getIDSocieta(),false,'url_dett'));
$tmpl->stampa();
