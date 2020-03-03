<?php
session_start();
require_once 'config.inc.php';
include_model('Federazione');
include_view('ListaFederazioni');

/**
 * @param Societa $soc
 */
function dati_url($soc) {
	return 'soc/dati.php?soc='.$soc->getId();
}

$body = "<h4>Federazioni</h4>";
$body .= "<ul>";
foreach(Federazione::elenco() as $id=>$fed)
{
	if($id == 1)
		continue;
	
	$n = $fed->getNome();
	$link = "<li><a href=\"listasoc.php?id_fed=2\">$n</a></li>";
	$body .= $link;
}
$body .= "</ul>";

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_SOC);
$tmpl->setTitolo('Riepilogo Federazioni');
$tmpl->addBody($body);
$tmpl->stampa();