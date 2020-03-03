<?php
session_start();
require_once 'config.inc.php';
include_view('SocietaIdonee');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_VAR);
$tmpl->setTitolo('Societ&agrave; idonee');
$tmpl->addBody(new SocietaIdonee());
$tmpl->stampa();
