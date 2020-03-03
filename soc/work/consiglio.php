<?php
session_start();
require_once 'config.inc.php';
include_view('VisualizzaConsiglio');

$tmpl = get_template();
$tmpl->setTitolo('Consiglio');
$tmpl->addBody(new VisualizzaConsiglio(Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();
