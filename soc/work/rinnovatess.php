<?php
session_start();
require_once 'config.inc.php';
include_view('RinnovaTesserato');

$tmpl = get_template();
$tmpl->setTitolo('Rinnova Tesserati');
$tmpl->addBody(new RinnovaTesserato(Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();
