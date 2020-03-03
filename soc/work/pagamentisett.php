<?php
session_start();
require_once 'config.inc.php';
check_get('idsett');
include_view('SaldoPagamentiSett');

$tmpl = get_template();
$tmpl->setTitolo('Saldo pagamenti - Settore');
$tmpl->addBody(new SaldoPagamentiSett(Auth::getUtente()->getIDSocieta(),$_GET['idsett']));
$tmpl->stampa();
