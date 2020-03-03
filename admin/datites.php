<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('DettagliTesserato');

$tmpl = get_template();
$tmpl->setTitolo('Dettagli tesserato');
$tmpl->addBody(new DettagliTesserato($_GET['id']));
$tmpl->stampa();
