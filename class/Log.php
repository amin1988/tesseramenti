<?php
if (!defined("_BASE_DIR_")) exit();
include_class('Database');

class Log {

	private static function checkLivello($livello)
	{
		return $livello <= LOG_LEVEL;
	}
	
	public static function error($note, $dettagli=NULL)
	{
		self::add(E_ERROR, $note, $dettagli);
	}
	
	public static function warning($note, $dettagli=NULL)
	{
		self::add(E_WARNING, $note, $dettagli);
	}
	
	public static function info($note, $dettagli=NULL)
	{
		self::add(E_INFO, $note, $dettagli);
	}
	
	public static function debug($note, $dettagli=NULL)
	{
		self::add(E_DEBUG, $note, $dettagli);
	}
	
	public static function add($livello, $note, $dettagli=NULL)
	{
		if(!self::checkLivello($livello)) return;

		$db = Database::get();
		$utente = Auth::getUtente();
		$livello = $db->quote($livello);
		$note = $db->quote($note);
		
		if($utente === NULL) $utente = "NULL";
		else
			$utente = "'".$db->quote($utente->getId())."'";
		
		if($dettagli === NULL) $dettagli = "NULL";
		else 
			$dettagli = "'".$db->quote(serialize($dettagli))."'";
		
		$sql = "INSERT INTO `log`(`idutente`, `livello`, `note`, `dettagli`)".
						" VALUES ($utente,'$livello','$note',$dettagli);";
		
		$db->query($sql, false);
	}
}