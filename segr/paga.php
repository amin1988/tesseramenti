<?php
session_start();
require_once 'config.inc.php';
check_get('id');
include_view('RegistraPagamento','SaldoPagamentiTot');

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_PAGA);
$tmpl->setTitolo('Registra pagamento');
$tmpl->addBody(new SaldoPagamentiTot($_GET['id']), '<div style="height:20px;"></div>', new RegistraPagamento($_GET['id']));
$tmpl->stampa();
