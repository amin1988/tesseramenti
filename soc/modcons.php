<?php
session_start();
require_once 'config.inc.php';
include_view('ModificaConsiglio');

function salvaCons() {
	redirect('soc/dati.php');
}

$tmpl = get_template();
$tmpl->setTitolo('Modifica consiglio');
$ids = Auth::getUtente()->getIDSocieta();
$tmpl->addBody(new ModificaConsiglio($ids, 'salvaCons'));
$tmpl->stampa();