<?php
session_start();
require_once 'config.inc.php';
include_view('SaldoPagamentiTot');

$tmpl = get_template();
$tmpl->setTitolo('Saldo pagamenti - Totali');
$tmpl->addBody(new SaldoPagamentiTot(Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();
