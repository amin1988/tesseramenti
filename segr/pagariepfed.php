<?php
session_start();
require_once 'config.inc.php';
include_view('ListaFederazioni');

/**
 * @param Federazione $fed
 */
function paga_url($fed) {
	return 'paga_lista.php?id_fed='.$fed->getId();
}

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_PAGA);
$tmpl->setTitolo('Riepilogo pagamenti Federazioni');
if(in_rinnovo())
{
	$anno = date('Y');
	
	//tabs
	$tmpl->addBody('<br><ul class="nav nav-tabs"><li><a href=".attuale" data-toggle="tab">Anno ' . $anno
			. '</a></li> <li class="active"><a href=".rinnovo" data-toggle="tab">Anno ' . ($anno+1)
			. '</a></li></ul>');
	$tmpl->addBody('<div class="tab-content"><div class="tab-pane attuale"><h2>Anno '.$anno.'</h2>',
			new ListaFederazioni('paga_url',$anno),
			'</div><div class="tab-pane active rinnovo"><h2>Anno '.($anno+1).'</h2>',
			new ListaFederazioni('paga_url',$anno+1),
			'</div></div>');
}
else 
{
	$anno = date('Y');
	$tmpl->addBody(new ListaFederazioni('paga_url',$anno));
}
$tmpl->stampa();
