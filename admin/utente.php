<?php
session_start();
require_once 'config.inc.php';
include_view('GestioneUtente');

if(isset($_GET['ut']))
	$ut = $_GET['ut'];
else
	$ut = 0;

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_GEST);
$tmpl->setTitolo("Gestione Utente");
$tmpl->addBody(new GestioneUtente($ut));
$tmpl->stampa();