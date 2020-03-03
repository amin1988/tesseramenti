<?php
//TODO fare una pagina fatta bene apposta

session_start();
require_once 'config.inc.php';

if (isset($_GET['id'])) {
	check_get('id');
	Auth::loginAs($_GET['id']);
	go_home();
}

include_view('ListaSocieta');

/**
 * @param Societa $soc
 */
function logas_url($soc) {
	return 'logas.php?id='.$soc->getId();
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_LOGAS);
$tmpl->setTitolo('Login mascherato');
$tmpl->addBody(new ListaSocieta('logas_url'));
$tmpl->stampa();
