<?php 
session_start();
require_once 'config.inc.php';
include_view('Arbitri');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_VAR);
$tmpl->setTitolo('Arbitri');
$tmpl->addBody(new Arbitri());
$tmpl->stampa();
