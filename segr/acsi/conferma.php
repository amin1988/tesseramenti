<?php
session_start();
require_once 'config.inc.php';
include_view('LettoreAcsi');

set_time_limit(60);

function dettagli_tess($idt) {
	return _PATH_ROOT_."segr/soc/tess.php?id=$idt";
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_ACSI);
$tmpl->setTitolo('Conferma tesseramenti');
$tmpl->addBody(new LettoreAcsi('dettagli_tess'));
$tmpl->stampa();


