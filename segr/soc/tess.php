<?php
session_start();
$hasoc = isset($_GET['soc']);
if (!$hasoc) $_GET['soc'] = '0';

require_once 'config.inc.php';
check_get('id');
include_view('DettagliTesserato');
$view = new DettagliTesserato($_GET['id'], NULL);
if (!$hasoc) $_GET['soc'] = $view->getIdSocieta();

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_TESS);
$tmpl->setTitolo('Dettagli tesserato');
$tmpl->addBody("<a href=\"tess-mod.php?soc=$_GET[soc]&id=$_GET[id]\" class=\"btn\">",
		'<i class="icon-pencil"></i> Modifica tesserato</a>',
		$view);
$tmpl->stampa();
