<?php
session_start();
require_once 'config.inc.php';
include_view('societa','VisualizzaConsiglio');

function modifica($url) {
	return "<a href=\"$url\" class=\"modifica\"><i class=\"icon-pencil\"></i> <span>Modifica</span></a>";
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_DATI);
$tmpl->setTitolo('Dati società');

$ids = Auth::getUtente()->getIDSocieta();
$tmpl->addBody(
		'<div class="row"><div class="span6 dati"><h3>Dati societ&agrave '.modifica('modsoc.php').'</h3>',
		new DettagliSocieta($ids),
		'</div><div class="span6 consiglio"><h3>Consiglio '.modifica('modcons.php').'</h3>',
		new VisualizzaConsiglio($ids),
		'</div></div>'
);
$tmpl->stampa();
