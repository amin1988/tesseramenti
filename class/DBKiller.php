<?php
if (!defined('_BASE_DIR_')) exit();
include_class('Database','Data');
include_model('Utente');

class DBKiller {
	
}

class DBKillerUtil {
	
	private static $inst = NULL;
	
	/**
	 * @return DBKillerUtil	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new DBKillerUtil();
		return self::$inst;
	}
	
	/**
	 * Elimina l'utente associato all'$id_utente passato come parametro
	 * L'utente viene eliminato solo se esiste almeno un altro utente per la stessa società
	 * Non vengono eliminati utenti admin e segreteria
	 * @param int $id_utente
	 * @param boolean $force
	 */
	public function eliminaUtente($id_utente,$force=false)
	{
		$db = Database::get();
		$fd = $db->field('utenti', 'idsocieta', "idutente='$id_utente'");
		$ids = intval($fd);
		if($ids != 0) //non elimino utenti admin
		{
			$rs = $db->select('utenti',"idsocieta='$ids'","COUNT(*)");
			$row = $rs->fetch_row();
			$n = $row[0];
			if($n>1 || $force) //non elimino se è l'unico utente della società
				$db->delete('utenti', "idutente='$id_utente'");
		}
	}
	
	/**
	 * Elimina la società associata all'$id_societa passato come parametro
	 * La società viene eliminata solo se non ha pagamenti per settori attivi per l'anno in corso
	 * @param int $id_societa
	 */
	public function eliminaSocieta($id_societa)
	{
		$db = Database::get();
		
		//se ha pagato per questo anno o per il prossimo
		$anno = DataUtil::get()->oggi()->getAnno();
		
		$anno_act = "$anno-12-31";
		$anno ++;
		$anno_nxt = "$anno-12-31";
		
		$rs = $db->select('pagamenti',"idsocieta='$id_societa' AND data_pagamento IS NOT NULL AND (scadenza='$anno_act' OR scadenza='$anno_nxt')");
		
		$row = $rs->fetch_assoc();
		if($row !== NULL)
			return; //ha dei pagamenti attivi
		
		//elimino gli utenti associati alla società
		$ar_ut = Utente::elenco("idsocieta='$id_societa'");
		foreach($ar_ut as $id_ut=>$ut)
			$this->eliminaUtente($id_ut,true);
		
		$db->delete('societa', "idsocieta='$id_societa'");
	}
}