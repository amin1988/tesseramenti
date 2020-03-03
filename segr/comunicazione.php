<?php 
session_start();
require_once 'config.inc.php';
include_view('Comunicazione');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_VAR);
$tmpl->setTitolo('Comunicazione');
$tmpl->addBody(new Comunicazione());
$tmpl->stampa();
