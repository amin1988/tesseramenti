<?php
session_start();
require_once 'config.inc.php';
include_view('ModificaSettori');

$tmpl = get_template();
$tmpl->setTitolo('Rinnova Societa');
$tmpl->addBody(new ModificaSettori(Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();

