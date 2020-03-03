<?php
session_start();
require_once 'config.inc.php';
check_get('id');
check_get('set');
include_view('ModificaCrediti');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_TESS);
$tmpl->setTitolo('Modifica crediti');
$tmpl->addBody(new ModificaCrediti($_GET['id'], $_GET['set']));
$tmpl->stampa();
