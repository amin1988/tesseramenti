<?php
session_start();
require_once 'config.inc.php';
include_view('societa','VisualizzaConsiglio','DettagliTesseratiSocieta');

function modifica($url, $nome) {
	return "<a href=\"$url?soc=$_GET[soc]\" class=\"btn\"><i class=\"icon-pencil\"></i> <span>Modifica $nome</span></a> ";
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_DATI);
$tmpl->setTitolo('Dati società');

$ids = $_GET['soc'];
$tmpl->addBody(
		'<div class="row"><div class="span6 dati"><h3>Dati societ&agrave</h3>',
		modifica('modsoc.php','dati societ&agrave;'), 
		//modifica('#','settori'), //TODO
		new DettagliSocieta($ids, true),
		'</div><div class="span6 consiglio"><h3>Consiglio</h3>',
		modifica('modcons.php','consiglio'),
		new VisualizzaConsiglio($ids),
		'<h3>Tesserati</h3>',
		new DettagliTesseratiSocieta($ids),
		'</div></div>'
);
$tmpl->stampa();
