<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello');

class Consiglio extends Modello {

	const PRESIDENTE = 'presidente';
	const VICEPRESIDENTE = 'vicepresidente';
	const SEGRETARIO = 'segretario';
	const CONSIGLIERE1 = 'consigliere_1';
	const CONSIGLIERE2 = 'consigliere_2';
	const CONSIGLIERE3 = 'consigliere_3';
	const CONSIGLIERE4 = 'consigliere_4';
	const DIRETTORETECNICO = 'dt';
	
	static function getRuoli() {
		return array(self::PRESIDENTE, self::VICEPRESIDENTE, self::SEGRETARIO, self::CONSIGLIERE1,
				self::CONSIGLIERE2, self::CONSIGLIERE3, self::CONSIGLIERE4, self::DIRETTORETECNICO);
	}
	
	static function getRuoliCheck() {
		return array(self::PRESIDENTE, self::VICEPRESIDENTE, self::SEGRETARIO, self::CONSIGLIERE1,
				self::CONSIGLIERE2, self::CONSIGLIERE3, self::CONSIGLIERE4);
	}
	
	static function getRuoloStr($ruolo) {
		switch ($ruolo)
		{
			case self::PRESIDENTE : return "Presidente"; break;
			case self::VICEPRESIDENTE : return "Vicepresidente"; break;
			case self::SEGRETARIO : return "Segretario"; break;
			
			case self::CONSIGLIERE1 :
			case self::CONSIGLIERE2 :
			case self::CONSIGLIERE3 :
			case self::CONSIGLIERE4 : return "Consigliere"; break;
			
			case self::DIRETTORETECNICO : return "D.T."; break;
		}
	}
	
	/**
	 * Resituisce true se il tesserato fa parte di qualche consiglio
	 * @param int $id_tess
	 * @return boolean
	 */
	public static function isMembro($id_tess)
	{
		$db = Database::get();
		$str = $db->field('tesserati_consiglio', 'idsocieta', "idtesserato='$id_tess'");
		
		if($str == NULL)
			return false;
		else 
			return true;
	}
		
	public function __construct($id=NULL) {
		parent::__construct('consiglio', 'idsocieta', $id);
	}
	
	function getPresidente() {
		return $this->get('presidente');
	}
	
	/**
	 * 
	 * @param $ruolo
	 * @return Tesserato
	 */
	function getMembro($ruolo) {
		$idmembro = $this->get($ruolo);
		
		if($idmembro!==NULL)
			return new Tesserato($idmembro);
		else return NULL;
	}
		
	/**
	 * 
	 * @param $ruolo
	 */
	function getIDMembro($ruolo) {
		return $this->get($ruolo);
	}
	
	function setMembro($ruolo, $idmembro)
	{
		if(is_object($idmembro))
			$idmembro = $idmembro->getId();
		
		$this->set($ruolo, $idmembro);
	}
	
	/**
	 * Elimina un membro del consiglio, senza sostituirlo, se questo non è obbligatorio
	 * @param $ruolo
	 */
	function eliminaMembro($ruolo)
	{
		if(in_array($ruolo, array(self::CONSIGLIERE1,self::CONSIGLIERE2,self::CONSIGLIERE3,self::CONSIGLIERE4)))
			$this->set($ruolo, NULL);
	}
	
	/**
	 * Restituisce l'id del DT
	 * @return integer
	 */
	function getIdDT() {
		return $this->get('dt');
	}
	
	function setDT($id_dt)
	{
		if(is_object($id_dt))
			$id_dt = $id_dt->getId();
		
		$this->set('dt', $id_dt);
	}
	
}
	