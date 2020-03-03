<?php 
session_start();
require_once 'config.inc.php';
include_view('IscrizioneAcsi');

set_time_limit(150);

function dettagli_tess($idt) {
	return _PATH_ROOT_."segr/soc/tess.php?id=$idt";
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_ACSI);
$tmpl->setTitolo('Invio tesseramenti');
$tmpl->addBody(new IscrizioneAcsi('filegen.php','dettagli_tess'));
$tmpl->stampa();


