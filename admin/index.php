<?php 
session_start();
require_once 'config.inc.php';
include_view('societa');

$txt = <<<HTML
Ciao admin!<br>
<a href="logas.php">Login mascherato</a><br>
<a href="datisoc.php?id=1">dati società 1</a><br>
<a href="datites.php?id=1">Dati tesserato 1</a><br>
<a href="instessacsi.php">Inserimento Tessere ACSI</a><br>
<a href="work/log.php">Log</a><br>
HTML;

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_HOME);
$tmpl->setTitolo('Area admin');
$tmpl->addBody($txt);
$tmpl->stampa();


