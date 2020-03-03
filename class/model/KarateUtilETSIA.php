<?php
if (!defined('_BASE_DIR_')) exit();

class KarateUtilETSIA {
	const TAB_KYU = 'karate_kyu_etsia';
	const KYU_NERA = 0;
	const KYU_LEV4 = 4;
	
	private static $inst = NULL;
	
	/**
	 * @return KarateUtilETSIA
	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new KarateUtilETSIA();
		return self::$inst;
	}
	
	/**
	 * Mantiene l'ID grado della cintura nera
	 * @var int
	 */
	private $idnera = NULL;
	
	/**
	 * Mantiene l'ID grado del EQF Level 4 Tutor
	 * @var int
	 */
	private $idlev4 = NULL;
	
	/**
	 * Converte un id grado nel rispettivo kyu 
	 * @param int $idgrado
	 * @return int valore da 0 (EQF Graduate 8) a 8 (Practitioner) o NULL se l'id non corrisponde
	 *  ad una cintura di karate 
	 */
	public function gradoToKyu($idgrado) {
		if ($idgrado == $this->idnera) return self::KYU_NERA;
		$kyu = Database::get()->field(self::TAB_KYU, 'kyu', "idgrado='$idgrado'");
		if ($kyu == self::KYU_NERA) $this->idnera = $idgrado;
		return $kyu;
	}
	
	/**
	 * Converte un kyu nel rispettivo id grado
	 * @param int $kyu valore da 0 (EQF Graduate 8) a 8 (Practitioner) o NULL se l'id non corrisponde
	 * @return int id grado corrispondente o NULL se $kyu non è un kyu
	 */
	public function kyuToGrado($kyu) {
		if ($kyu == self::KYU_NERA && $this->idnera !== NULL)
			return $this->idnera;
		 
		$idg = Database::get()->field(self::TAB_KYU, 'idgrado', "kyu='$kyu'");
		if ($kyu == self::KYU_NERA) $this->idnera = $idg;
		return $idg;
	}
	
	/**
	 * Converte un array di kyu nei rispettivi id grado
	 * @param int[] $kyu_list array di valori da 0 (EQF Graduate 8) a 8 (Practitioner) o NULL se l'id non corrisponde
	 * @return int[] formato kyu => idgrado
	 */
	public function listaKyuToGradi($kyu_list) {
		if (!is_array($kyu_list) || count($kyu_list) == 0) return array();
		
		$db = Database::get();
		$ka = $db->quoteArray($kyu_list);
		$rs = Database::get()->select(self::TAB_KYU, "kyu IN $ka", 'kyu, idgrado');
		$res = array();
		while($row = $rs->fetch_assoc()) {
			$res[$row['kyu']] = $row['idgrado'];
			if ($row['kyu'] == self::KYU_NERA)
				$this->idnera = $row['idgrado'];
		}
		return $res;
	}
	
	/**
	 * Restituisce l'id grado relativo alla cintura nera
	 */
	public function getIdNera() {
		if ($this->idnera === NULL) 
			return $this->kyuToGrado(self::KYU_NERA);
		else
			return $this->idnera;
	}
	
	public function getIdLevel4() {
		if ($this->idlev4 === NULL)
			return $this->kyuToGrado(self::KYU_LEV4);
		else
			return $this->idlev4;
	}
}