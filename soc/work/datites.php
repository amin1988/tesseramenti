<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('DettagliTesserato');

$tmpl = get_template();
$tmpl->setTitolo('Dettagli tesserato');
$txt = <<<HTML
<a href="modtes.php?id=1">Modifica dati tesserato 1</a><br>
HTML;
$tmpl->addBody($txt,new DettagliTesserato($_GET['id'], Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();
