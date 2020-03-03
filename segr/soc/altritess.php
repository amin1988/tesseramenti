<?php
session_start();
require_once 'config.inc.php';
include_view('RinnovaTesserati');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_TESS);
$tmpl->setTitolo('Altri tesserati');

$ids = $_GET['soc'];
$tmpl->addBody(new RinnovaTesserati($ids));
$tmpl->stampa();
