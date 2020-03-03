<?php
session_start();
require_once 'config.inc.php';
include_view('GeneraTesserini');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_ACSI);
$tmpl->setTitolo('Gestione Tessetini');
$tmpl->addBody(new GeneraTesserini());
$tmpl->stampa();