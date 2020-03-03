<?php
session_start();
require_once 'config.inc.php';
include_view('ModificaConsiglio','NuovoTessPop');

$tmpl = get_template();
$tmpl->setTitolo('Modifica consiglio');
$ids = Auth::getUtente()->getIDSocieta();
$tmpl->addBody(new NuovoTessPop($ids, NULL), new ModificaConsiglio($ids));
$tmpl->stampa();

