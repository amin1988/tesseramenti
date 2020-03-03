<?php
if (!defined('_BASE_DIR_')) exit();

class Data {
	const ANNO = 0;
	const MESE = 1;
	const GIORNO = 2;
	
	/**
	 * @var int[]
	 */	
	private $dt;

	/**
	 * @deprecated 
	 * @see DataUtil::oggi()
	 */
	public static function oggi() {
		return DataUtil::get()->oggi();
	}
	
	/**
	 * @deprecated 
	 * @see DataUtil::parseDMY()
	 */
	public static function parseDMY($valore) {
		return DataUtil::get()->parseDMY($valore);
	}
	
	/**
	 * @deprecated 
	 * @see Data::__construct
	 * @return Data
	 */
	public static function daValori($giorno, $mese, $anno) {
		return new Data($anno, $mese, $giorno);
	} 
	
	/**
	 * Crea una nuova data
	 * @param int $anno
	 * @param int $mese da 1 a 12
	 * @param int $giorno da 1 a 31
	 */
	public function __construct($anno, $mese, $giorno) {
		$this->dt[self::ANNO] = $anno; 
		$this->dt[self::MESE]  = $mese;
		$this->dt[self::GIORNO]  = $giorno;
	}
	
	/**
	 * @param Data $data
	 * @return int 0 se la data è uguale, -1 se questa data è precedente, 1 se è successiva
	 */
	public function confronta($data) {
		for ($i=0; $i < 3; $i++) { 
			if ($this->dt[$i] < $data->dt[$i])
				return -1;
			if ($this->dt[$i] > $data->dt[$i])
				return 1;
		}
		return 0;
	}
	
	/**
	 * @return boolean
	 */
	public function valida() {
		return checkdate($this->dt[self::MESE], $this->dt[self::GIORNO], $this->dt[self::ANNO]);
	}
	
	/**
	 * Indica se questa data � una data futura (oggi escluso)
	 * @return boolean
	 */
	public function futura() {
		return $this->confronta(self::oggi()) > 0;
	}
	
	/**
	 * Indica se questa data � una data passata (oggi escluso)
	 * @return boolean
	 */
	public function passata() {
		return $this->confronta(self::oggi()) < 0;
	}
	
	public function format($format) {
// 		return date($format, mktime(0,0,0,$this->dt[1], $this->dt[2], $this->dt[0]));
    	$spf = str_replace(array("d","m","Y"),array("%3$02d","%2$02d","%1$04d"),$format);
   		return sprintf($spf, $this->dt[self::ANNO], $this->dt[self::MESE], $this->dt[self::GIORNO]);
	}
	
	/**
	 * Crea una data rappresentante il gorno successivo a questa
	 */
	public function successiva() {
		$anno = $this->dt[self::ANNO];
		$mese = $this->dt[self::MESE];
		$giorno = $this->dt[self::GIORNO]+1;
		
		if (!checkdate($mese, $giorno, $anno)) {
			$giorno = 1;
			$mese++;
			if ($mese > 12) {
				$mese = 1;
				$anno++;
			}
		}
		return new Data($anno, $mese, $giorno);
	}
	
	/**
	 * @param Data $data
	 * @param boolean $millesimo
	 * @return int
	 */
	public function anniDa($data,$millesimo=true) {
		if ($millesimo || $this->dt[self::MESE] < $data->dt[self::MESE])
			return $data->dt[self::ANNO] - $this->dt[self::ANNO]; 
    	//controllo preciso se $millesimo==false
    	$eta = $data->dt[self::ANNO] - $this->dt[self::ANNO];
    	if ($this->dt[self::MESE] > $data->dt[self::MESE])
    		return $eta - 1;
    	//stesso mese
    	if ($this->dt[self::GIORNO] > $data->dt[self::GIORNO])
    		return $eta - 1;
    	 else
    	 	return $eta;
	}
	
	/**
	 * @return int
	 */
	public function getGiorno() {
		return $this->dt[self::GIORNO];
	}
	
	/**
	 * @return int
	 */
	public function getMese() {
		return $this->dt[self::MESE];
	}
	
	/**
	 * @return int
	 */
	public function getAnno() {
		return $this->dt[self::ANNO];
	}
	
	/**
	 * Converte questa data in formato dd/mm/yyyy
	 * @return string
	 */
	public function toDMY() {
		return sprintf('%02d/%02d/%04d',$this->dt[self::GIORNO], $this->dt[self::MESE], $this->dt[self::ANNO]);
	}
	
	/**
	 * Converte questa data in formato SQL
	 * @return string data formato yyyy-mm-dd
	 */
	public function toSQL() {
		return sprintf('%04d-%02d-%02d',$this->dt[self::ANNO], $this->dt[self::MESE], $this->dt[self::GIORNO]);
	}
	
	/**
	 * @return string data formato yyyy-mm-dd
	 */
	public function toString() {
		return sprintf('%04d-%02d-%02d',$this->dt[self::ANNO], $this->dt[self::MESE], $this->dt[self::GIORNO]);
	}
}

class DataUtil {
	private static $inst = NULL;
	private $oggi = NULL;
	
	/**
	 * @return DataUtil	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new DataUtil();
		return self::$inst;
	}
	
	/**
	 * Restituisce la data di oggi
	 * @return Data
	 */
	public function oggi() {
		if ($this->oggi === NULL) {
			$v = getdate();
			$this->oggi = new Data($v['year'], $v['mon'], $v['mday']);
		}
		return $this->oggi;
	}
	
	/**
	 * Crea una data da una stringa SQL
	 * @param string $data formato yyyy-mm-dd
	 */
	public function fromSql($data) {
		$e = preg_split('/\D/', $data);
		return new Data(intval($e[0]), intval($e[1]), intval($e[2]));
	}
	
	/**
	 * Legge una data in formato dd/mm/yyyy
	 * @param string $valore
	 * @return Data o NULL se la stringa non è valida
	 */
	public function parseDMY($valore) {
		if (!preg_match('/^(\d\d?)\/(\d\d?)\/(\d{4})$/', trim($valore), $m))
			return NULL;
		return new Data($m[3], $m[2], $m[1]);
	}
	
}
 
