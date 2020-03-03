<?php
session_start();
require_once 'config.inc.php';

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SOC_MODULI);
$tmpl->setTitolo('Modulistica');

$tmpl->addBody('<div class="text-center"><a href="trattdati.php" target="_blank">Dichiarazione di consenso al trattamento dei dati personali</a><br><br>');
$anno = date('Y');
if (in_rinnovo()) {
	$rinn = $anno+1;
	$tmpl->addBody("<a href=\"privacy.php?magg=0&anno=$rinn\" target=\"_blank\">Modello Privacy Minorenni $rinn</a><br>\n"
			."<a href=\"privacy.php?magg=1&anno=$rinn\" target=\"_blank\">Modello Privacy Maggiorenni $rinn</a><br><br>\n"
			."<a href=\"privacy.php?magg=0&anno=$anno\" target=\"_blank\">Modello Privacy Minorenni $anno</a><br>\n"
			."<a href=\"privacy.php?magg=1&anno=$anno\" target=\"_blank\">Modello Privacy Maggiorenni $anno</a>\n");
} else {
	$tmpl->addBody("<a href=\"privacy.php?magg=0&anno=$anno\" target=\"_blank\">Modello Privacy Minorenni</a><br>\n"
			."<a href=\"privacy.php?magg=1&anno=$anno\" target=\"_blank\">Modello Privacy Maggiorenni</a>\n");
}
$tmpl->addBody('</div>');
$tmpl->stampa();
