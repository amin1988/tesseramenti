<?php
session_start();
require_once 'config.inc.php';
include_view('ListaSocieta','societa');

/**
 * @param Societa $soc
 */
function soc_url($soc) {
	return _PATH_ROOT_.'admin/societa.php?soc='.$soc->getId();
}
if(!isset($_GET['soc']))
{
	$tmpl = get_template();
	$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_GEST);
	$tmpl->setTitolo("Gestione Societa");
	$tmpl->addBody(new ListaSocieta('soc_url',NULL, true));
	$tmpl->stampa();
}
else 
{
	$idsoc = $_GET['soc'];
	$but_eli = "<a href=\"elimina.php?id_societa=$idsoc\" class=\"btn btn-danger\">Elimina</a>";
	
	$tmpl = get_template();
	$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_GEST);
	$tmpl->setTitolo("Gestione Societa");
	$tmpl->addBody(new DettagliSocieta($idsoc), $but_eli);
	$tmpl->stampa();
}