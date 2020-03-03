<?php
session_start();
require_once 'config.inc.php';

$tmpl = get_template();
$tmpl->setAttr(TMPL_ATTR_SEZ, SEZ_ADMIN_LOG);
$tmpl->setTitolo('Log');
$tmpl->addBody(listaLog());
$tmpl->stampa();


function checkPost($key) {
	return isset($_GET[$key]) && trim($_GET[$key]) != '';
}

function lvToString($lv) {
	switch ($lv) {
		case E_ERROR: return 'Errore';
		case E_WARNING: return 'Avviso';
		case E_INFO: return 'Info';
		case E_DEBUG: return 'Debug';
	}
}

function listaLog() {
	include_class('Database');
	
	$r = <<<HTML1
<form method="get" class="form-inline">
<b>Filtri:</b><br>
Utente: <input type="text" name="utente"> <label><input type="checkbox" name="no_utente"> Negato</label><br>
Note: <input type="text" name="note"> <label><input type="checkbox" name="no_note"> Negato</label><br>
Livelli: <select name="lv">
<option value="">Tutti</option>
HTML1;
	$lv = array(E_ERROR,E_WARNING,E_INFO,E_DEBUG);
	if (checkPost('lv')) $lvs = $_GET['lv'];
	else $lvs = '';
	foreach ($lv as $k) {
		$v = lvToString($k);
		$r .= "<option value=\"$k\"";
		if ($k == $lvs) $r .= ' selected';
		$r .= ">$v</option>\n";
	}
	$r .= <<<HTML2
</select><br>
Data: <input type="text" name="data"><br>
<input type="submit">
</form>

<table class="table table-border table-striped"><thead>
<tr>
<th>ID</th>
<th>ID Utente</th>
<th>Utente</th>
<th>Livello</th>
<th>Note</th>
<th>Data</th>
</tr>
</thead><tbody>
HTML2;
	
	$db = Database::get();
	$where = '1 ';
	if (checkPost('utente')) {
		$v = $db->quote($_GET['utente']);
		if (checkPost('no_utente')) $cmp = '!=';
		else $cmp = '='; 
		$where .= " AND idutente $cmp '$v'";
	}
	if (checkPost('lv')) {
		$v = $db->quote($_GET['lv']);
		$where .= " AND livello <= '$v'";
	}
	if (checkPost('note')) {
		$v = $db->quote($_GET['note']);
		if (checkPost('no_note')) $cmp = 'NOT';
		else $cmp = ''; 
		$where .= " AND note $cmp LIKE '$v'";
	}
	if (checkPost('data')) {
		$v = $db->quote($_GET['data']);
		$where .= " AND data LIKE '$v'";
	}
	
	$rs = $db->select('log l LEFT JOIN utenti u USING(idutente)',"$where ORDER BY data DESC, idlog DESC LIMIT 100", 'l.*, u.username');
	if ($rs != NULL) {
		while($row = $rs->fetch_assoc()) {
			$lv = lvToString($row['livello']);
			$r .= "<tr><td>$row[idlog]</td><td>$row[idutente]</td><td>$row[username]</td>";
			$r .= "<td>$lv</td><td>$row[note]</td><td>$row[data]</td></tr>\n";
			$r .= '<tr><td colspan="7"><span style="font-family:monospace;">';
			$r .= str_replace(array("\t",'  '), array('&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;'), nl2br(htmlspecialchars(var_export(unserialize($row['dettagli']), true))));
			$r .= '</span></td></tr>';
		}
	}
	$r .= '</tbody></table>';
	
	return $r;
}
	