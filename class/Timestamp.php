<?php
if (!defined('_BASE_DIR_')) exit();
include_class('Data');

class Timestamp extends Data {
	const H = 0;
	const MIN = 1;
	const SEC = 2;
	
	private $time;
	
	/**
	 * Crea un nuovo timestamp
	 * @param int $anno
	 * @param int $mese
	 * @param int $giorno
	 * @param int $ora [def:0]
	 * @param int $min [def:0]
	 * @param int $sec [def:0]
	 */
	public function __construct($anno, $mese, $giorno, $ora=0, $min=0, $sec=0) {
		parent::__construct($anno, $mese, $giorno);
		$this->time[self::H] = $ora;
		$this->time[self::MIN] = $min;
		$this->time[self::SEC] = $sec;
	}
	
	/**
	 * @return int
	 */
	public function getOra() {
		return $this->time[self::H];
	}
	
	/**
	 * @return int
	 */
	public function getMinuti() {
		return $this->time[self::MIN];
	}
	
	/**
	 * @return int
	 */
	public function getSecondi() {
		return $this->time[self::SEC];
	}
	
	public function valida() {
		$h = $this->time[self::H];
		if ($h < 0 || $h > 23) return false;
		$m = $this->time[self::MIN];
		if ($m < 0 || $m > 59) return false;
		$s = $this->time[self::SEC];
		if ($s < 0 || $s > 59) return false;
		return parent::valida();
	}
	
	/**
	 * @param Timestamp $ts
	 * @return int 0 se il timestamp è uguale, -1 se questo timestamp è precedente, 1 se è successivo
	 */
	public function confronta($ts) {
		$c = parent::confronta($ts);
		if ($c != 0) return $c;
		for ($i=0; $i < 3; $i++) { 
			if ($this->time[$i] < $ts->time[$i])
				return -1;
			if ($this->time[$i] > $ts->time[$i])
				return 1;
		}
		return 0;
	}
	
	public function format($format) {
		return date($format, $this->toUnix());
	}
	
	/**
	 * Restituisce il numero di secondi passati da un altro timestamp
	 * @param Timestamp $ts
	 * @return int
	 */
	public function secondiDa($ts) {
		return $this->toUnix() - $ts->toUnix();
	}
	
	/**
	 * @return int
	 */
	public function toUnix() {
		return mktime($this->time[self::H],$this->time[self::MIN],
				$this->time[self::SEC], $this->getMese(), $this->getGiorno(), $this->getAnno());
	}
	
	/**
	 * @return string data formato yyyy-mm-dd hh:mm:ss
	 */
	public function toString() {
		return parent::toString() . implode(':', $this->time);
	}
	
	/**
	 * Converte questo timestamp in formato SQL
	 * @return string timestamp formato yyyy-mm-dd hh:mm:ss
	 */
	public function toSQL() {
		return sprintf('%s %04d-%02d-%02d', parent::toSQL(),
				$this->time[self::H], $this->time[self::MIN], $this->time[self::SEC]);
	}
}

class TimestampUtil {
	private static $inst = NULL;
	
	/**
	 * @return TimestampUtil
	 */
	public static function get() {
		if (self::$inst === NULL) self::$inst = new TimestampUtil();
		return self::$inst;
	}
	
	/**
	 * Restitusice un timestamp che rappresenta questo momento
	 * @return Timestamp
	 */
	public function adesso() {
		$v = getdate();
		return new Timestamp($v['year'], $v['mon'], $v['mday'], 
				$v['hours'], $v['minutes'], $v['seconds']);
	}
	
	/**
	 * Crea un timestamp da un valore SQL
	 * @param string $val formato 'yyyy-mm-dd hh:mm:ss'
	 */
	public function fromSql($val) {
		$e = preg_split('/\D+/', $val);
		for($i=count($e); $i<6; $i++)
			$e[$i] = 0;
		return new Timestamp(intval($e[0]), intval($e[1]), intval($e[2]),
				intval($e[3]), intval($e[4]), intval($e[5]));
	}
	
	/**
	 * Crea un timestamp da una stringa in formato hh:mm[:ss]
	 * @param string $str
	 * @param Data $data [opz] la data da utilizzare per il timestamp
	 */
	public function parse($str, $data=NULL) {
		$t = explode(':', $str);
		//se non ci sono almeno ora e min esce
		if ($t < 2) return NULL;
		//se non sono numeri esce
		for($i=0; $i<3 && $i<count($t); $i++) {
			if (!is_numeric($t[$i])) return NULL;
		}
		if ($data === NULL) {
			$anno = $mese = $giorno = 0;
		} else {
			$anno = $data->getAnno();
			$mese = $data->getMese();
			$giorno = $data->getGiorno();
		}
		if (isset($t[2]))
			$s = $t[2];
		else
			$s = 0;
		return new Timestamp($anno, $mese, $giorno, $t[0], $t[1], $s);
	}
}