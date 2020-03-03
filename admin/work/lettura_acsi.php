<?php
session_start();
require_once 'config.inc.php';
include_controller('LettoreAcsi');


function lettore() {
	$html = '<form method="post" enctype="multipart/form-data"><input type="file" name="userfile"><input type="submit"></form>';
	$html .= print_r($_FILES,true);
	
	if (!isset($_FILES['userfile']['tmp_name'])) return $html;
	
	$html .= "file ricevuto";
	$file = $_FILES['userfile']['tmp_name'];
	$ctrl = new LettoreAcsiCtrl($file);
	return $html;
	$res = $ctrl->leggiFile($file);
	if ($res === NULL) return $html." errore";
	$html .= "<ul>";
	foreach ($res[LettoreAcsiCtrl::OK] as $a) {
		$idt = $a->getIDTesserato();
		$tes = $a->getTessera();
		$vda = $a->getValidoDa()->toDMY();
		$va = $a->getValidoA()->toDMY();
		$html .= "<li>IDt $idt - # $tes - da $vda a $va\n";
	}
	$html .= "</ul>";
	unset($res[LettoreAcsiCtrl::OK]);
	$html .= "<pre>".print_r($res,true)."</pre>";
	return $html;
}

$tmpl = get_template();
$tmpl->setTitolo('Lettura file ACSI');
$tmpl->addBody(lettore());
$tmpl->stampa();
