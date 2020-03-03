<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('ModificaSocieta');

function url_dett($soc) {
	$id = $soc->getId();
	redirect("admin/datisoc.php?id=$id");
}

$tmpl = get_template();
$tmpl->setTitolo('Modifica societa');
$tmpl->addBody(new ModificaSocieta($_GET['id'],true,'url_dett'));
$tmpl->stampa();
