<?php
session_start();
require_once 'config.inc.php';
include_view('InsTessACSI');

if(isset($_SESSION["NUM_TESS_AGG"]))
	$tot = $_SESSION["NUM_TESS_AGG"];
else 
	$tot = 0;

$modal = <<<MODAL
<div id="Modal_cosa" class="modal hide fade">
	<div class="modal-header" style="background: #f5f5f5;">
		<h3>Tessere Aggiunte</h3>
	</div>
	<div class="modal-body">
		<p>Sono state aggiunte $tot tessere</p>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">OK</button>
	</div>
</div>
MODAL;

$tmpl = get_template();
$tmpl->setTitolo('Inserimento tessere ACSI');
if(isset($_SESSION["NUM_TESS_AGG"]))
{
	unset($_SESSION["NUM_TESS_AGG"]);
	$tmpl->addJsOnload('$( "#Modal_cosa" ).modal({ show: true });');
}
$tmpl->addBody(new InsTessACSI());
$tmpl->addBody($modal);
$tmpl->stampa();
