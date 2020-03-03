<?php 
session_start();
require_once 'config.inc.php';
include_view('SpostaTesserati');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_VAR);
$tmpl->setTitolo('Sposta Tesserati');
$tmpl->addBody(new SpostaTesserati());
$tmpl->stampa();
