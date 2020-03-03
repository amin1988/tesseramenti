<?php
session_start();
require_once 'config.inc.php';
include_model('Assicurazione');

$db = Database::get();
$rs = $db->select('assicurazioni_invii',"idtesserato IS NOT NULL");
$count_i = 0;

while($row = $rs->fetch_assoc())
{
	$oggi = DataUtil::get()->oggi();
	$a = Assicurazione::crea($row['idtesserato'], $row['tessera'], $oggi);
	$a->salva();
	$count_i++;
}

//$rs = $db->select('assicurazioni',"valido_a='2015-12-31'");
$rs = $db->select('assicurazioni_invii',"idtesserato IS NOT NULL");
$count_e = 0;

while($row = $rs->fetch_assoc())
{
	$idtesserato = $row['idtesserato'];
	if($db->delete('assicurazioni_invii', "idtesserato='$idtesserato'"))
		$count_e++;
}

echo "Assicurazioni create: $count_i<br>Invii cancellati: $count_e";


