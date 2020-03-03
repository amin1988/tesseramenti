<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('societa','VisualizzaConsiglio');

$tmpl = get_template();
$tmpl->setTitolo('Dettagli societa');
$tmpl->addBody(new DettagliSocieta($_GET['id'], true), new VisualizzaConsiglio($_GET['id']));
$tmpl->stampa();
