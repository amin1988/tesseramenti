<?php 
session_start();
require_once 'config.inc.php';

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_HOME);
$tmpl->setTitolo('Area admin');
$tmpl->stampa();


