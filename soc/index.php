<?php 
session_start();
require_once 'config.inc.php';
include_view('societa');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_HOME);
$tmpl->setTitolo('Area società');
$txt = <<<HTML
Ciao soc!<br>
<a href="work/consiglio.php">Consiglio</a><br>
<a href="work/modconsiglio.php">Modifica consiglio</a><br>
<a href="work/datites.php?id=1">Dati tesserato 1</a><br>
<a href="work/nuovotess.php">Nuovo tesserato</a><br>
<a href="work/rinnova.php">Rinnova settori</a><br>
<a href="work/listates.php?idt=1">Lista tesserati</a><br>
<a href="work/pagamentitot.php">Pagamenti totali</a><br>
<a href="work/pagamentisett.php?idsett=1">Pagamenti settore 1</a><br>

HTML;
if (_LOCALHOST_)
	$tmpl->addBody($txt);
$msg = "<div class=\"alert alert-info\"><center><h5>Al fine di consentire l'assicurazione degli atleti, alle societ&agrave &egrave richiesto l'inserimento del codice fiscale per ogni tesserato.</h5></center></div>";
$tmpl->addBody($msg,new DettagliSocieta(Auth::getUtente()->getIDSocieta()));
$tmpl->stampa();
