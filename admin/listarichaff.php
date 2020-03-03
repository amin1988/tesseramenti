<?php
session_start();
require_once 'config.inc.php';
include_view('ListaRichiesteAff');


/**
 * @param RichiestaAff $rich
 */
function url_dett($rich) {
	$id = $rich->getId();
	return "datirich.php?id=".$id;
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_RICHIESTE);
$tmpl->setTitolo("Lista richieste affiliazione");
$tmpl->addBody(new ListaRichiesteAff('url_dett'));
$tmpl->stampa();