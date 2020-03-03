<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('ModificaRichiestaAff');

function submit_premuto($op) {
	redirect("admin/listarichaff.php?op=$op");
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_RICHIESTE);
$tmpl->setTitolo('Richiesta affiliazione');
$tmpl->addBody(new ModificaRichiestaAff($_GET['id'],'submit_premuto'));
$tmpl->stampa();
