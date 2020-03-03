<?php
require_once '../../config.inc.php';
check_get('soc');
require_once '../config.inc.php';
$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_SEGR_SOC);