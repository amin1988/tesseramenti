<?php
session_start();
require_once 'config.inc.php';
include_view('SaldoPagamentiTot','SaldoPagamentiSett','SaldaPagamenti');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SUBSEZ, SEZ_SEGR_SOC_PAGA);
$tmpl->setTitolo('Pagamenti');

$idsoc = $_GET['soc'];
$tmpl->addBody('<div class="text-center"><a href="../paga.php?id='.$idsoc.'" class="btn">Registra pagamento</a></div>');
if (in_rinnovo()) {
	$anno_att = date('Y');
	
	//tabs
	$tmpl->addBody('<br><ul class="nav nav-tabs"><li><a href="#attuale" data-toggle="tab">Anno ' . $anno_att
			. '</a></li> <li class="active"><a href="#rinnovo" data-toggle="tab">Anno ' . ($anno_att+1)
			. '</a></li></ul>');
	$tmpl->addBody('<div class="tab-content"><div class="tab-pane" id="attuale"><h2>Anno '.$anno_att.'</h2>',
			new SaldoPagamentiTot($idsoc, 0), new SaldoPagamentiSett($idsoc, 0),
			'</div><div class="tab-pane active" id="rinnovo"><h2>Anno '.($anno_att+1).'</h2>',
			new SaldoPagamentiTot($idsoc, 1), new SaldoPagamentiSett($idsoc, 1),
			'</div></div>');
	
} else {
	$tmpl->addBody(new SaldoPagamentiTot($idsoc, 0), new SaldoPagamentiSett($idsoc, 0));
}
$tmpl->stampa();
