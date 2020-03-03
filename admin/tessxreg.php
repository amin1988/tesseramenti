<?php
session_start();
require_once 'config.inc.php';
include_view('TesseratiRegioni');

check_get("anno");

$tmpl = get_template();
$tmpl->setTitolo('Tesserati per regioni');
$tmpl->addBody(new TesseratiRegioni($_GET["anno"]));
$tmpl->stampa();