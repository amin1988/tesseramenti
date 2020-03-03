<?php
session_start();
require_once 'config.inc.php';
check_get('idt');
include_view('TabTipiTesserati','ListaTesserati');

function url_tab($idtipo) {
	return '?idt='.$idtipo;
}

/**
 * @param Tesserato $tes
 */
function url_dett($tes) {
	//return  _PATH_ROOT_.'soc/tess.php?id='.$tes->getId();
	$id = $tes->getId();
	return "javascript:popupDett($id)";
}

/**
 * @param Tesserato $tes
 */
function dip_url($tes) {
	return _PATH_ROOT_.'soc/tess.php?id='.$tes->getId();
}

$ids = Auth::getUtente()->getIDSocieta();
$idt = $_GET['idt'];

$but_nuovo = '<a href="'._PATH_ROOT_.'soc/nuovo.php" class="btn">Nuovo tesserato</a>';
$js = <<<JS
function popupDett(id) {
	var dial = $( "#dialog-dett" );
	var dialbody = dial.children(".modal-body");
	dialbody.children(".dettagli").empty();
	dialbody.children(".attesa").show();
	$('#mod-tess').attr('href','tess-mod.php?id='+id);
	$.get(path_ajax("tess"), {idt: id}, function(data) {
			var dialbody = $( "#dialog-dett .modal-body" );
			dialbody.children(".attesa").hide();
			dialbody.children(".dettagli").append($(data));
		}, "html");
	
	dial.modal('show');
}
JS;

$modal = <<<MODAL
<div id="dialog-dett" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Dettagli tesserato</h3>
	</div>
	<div class="modal-body">
		<div class="attesa"></div>
		<div class="dettagli"></div>
	</div>
	<div class="modal-footer">
		<a id="mod-tess" class="btn"><i class="icon-pencil"></i> Modifica</a>
	</div>
</div>
MODAL;

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_TESS);
$tmpl->setTitolo('Lista tesserati');

$tmpl->addJsCode($js);
$tmpl->addJsOnload('$( "#dialog-dett" ).modal({ show: false });');

$tmpl->addBody(new TabTipiTesserati($ids, $idt, 'url_tab'));
$tmpl->addBody($but_nuovo,new ListaTesserati($ids, $idt, 'url_dett','dip_url'),$but_nuovo);
$tmpl->addBody($modal);
$tmpl->stampa();
