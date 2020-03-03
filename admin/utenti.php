<?php
session_start();
require_once 'config.inc.php';
include_view('ListaUtenti');

/**
 * @param Utente $ut
 */
function ut_url($ut) {
	return _PATH_ROOT_.'admin/utente.php?ut='.$ut->getId();
}

if(isset($_GET['ut']))
	$ut = $_GET['ut'];
else
	$ut = NULL;

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_GEST);
$tmpl->setTitolo("Gestione Utenti");
$tmpl->addBody(new ListaUtenti('ut_url'));
$tmpl->stampa();