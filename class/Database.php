<?php
if (!defined("_BASE_DIR_")) exit();
include_class('Log');

interface DbResult {
	function fetch_row();
	function fetch_assoc();
}

class Database {
	private static $db = NULL;
	
	/**
	 * connessione al database
	 * @var mysqli
	 */
	protected $conn;
	
	/**
	 * @return Database
	 */
	public static function get() {
		if (self::$db === NULL) {
			require _BASE_DIR_ . _DB_CONFIG_;
			if (!isset($port)) {
				$port = ini_get("mysqli.default_port");
			}
			self::$db = new Database($host, $user, $password, $database, $port);
		}
		return self::$db;
	}

	/**
	 * @param Database $db
	 */
	public static function set($db) {
		self::$db = $db;
	}

	/**
	 * Chiude la connessione al database
	 */
	public static function closeConn() {
		if (self::$db === NULL) return;
		self::$db->close();
		self::$db = NULL;
	}
	
	public function __construct($host, $user, $password, $database, $port) {
		$this->conn = new mysqli($host, $user, $password, $database, $port);
		$this->conn->set_charset('utf8');
	}
	
	function close() {
		$this->conn->close();
	}
	
	function error() {
		return $this->conn->error;
	}

	/**
	 * @access public
	 * @param string sql
	 * @return DbResult
	 */
	public function query($sql, $log=true, $error=false) {
		$val = $this->conn->query($sql);
		if($log && $val == false) {
			$err = $this->error();
			Log::error("SQL ERROR", array('sql'=>$sql, 'error'=>$err));
			if ($error) trigger_error($err);
		}

		return $val;
	}

	/**
	 * Esegue un comando SELECT del tipo <br>
	 * <code>SELECT $colonne FROM $tabella WHERE $where;</code>
	 * @param string tabella il nome della tabella da leggere
	 * @param string where [opz] il contenuto della formula WHERE
	 * @param string colonne [opz] le colonne da leggere
	 * @return DbResult
	 */
	public function select($tabella, $where = "1", $colonne = "*") {
		$rs = $this->query("SELECT $colonne FROM $tabella WHERE $where;", true, true);
		return $rs;
	}
	
	/**
	 * Legge il valore in una cella eseguendo una SELECT del tipo <br>
	 * <code>SELECT $colonna FROM $tabella WHERE $where LIMIT 1;</code>
	 * @param string tabella il nome della tabella da leggere
	 * @param string colonna la colonna da leggere
	 * @param string where [opz] il contenuto della formula WHERE
	 * @return string il valore nella colonna o NULL se non è stata selezionata nessuna riga
	 */
	public function field($tabella, $colonna, $where = '1') {
		$rs = $this->conn->query("SELECT $colonna FROM $tabella WHERE $where LIMIT 1;");
		/* @var $rs mysqli_result */
		$row = $rs->fetch_row();
		if ($row === NULL || count($row) == 0) return NULL;
		else return $row[0];
	}
	
	/**
	 * Esegue un comando INSERT 
	 * @param string $tabella il nome della tabella in cui inserire i valori
	 * @param array $valori un array contenente le coppie colonna => valore
	 */
	public function insert($tabella, $valori) {
		if (!is_array($valori) || count($valori) == 0) return true;
		$query = $this->insertSql($tabella, $valori);
		Log::debug("SQL INSERT", $query);
		return $this->query($query);
	}
	
	/**
	 * @param bool $chiudi [def:true] true per terminare il comando con ; 
	 * @see Database::insert
	 * @return string
	 */
	protected function insertSql($tabella, $valori, $chiudi=true) {
		$primo = true;
		foreach($valori as $c => $v){
			if ($v === NULL) $v = "NULL";
			else $v = "'".$this->quote($v)."'";
			if ($primo) {
				$sc = $c;
				$sv = $v;
				$primo = false;
			} else {
				$sc .= ", $c";
				$sv .= ", $v";
			}
		}
		if ($chiudi) $c = ';';
		else $c = '';
		return "INSERT INTO $tabella($sc) VALUES($sv)$c";
	}
	
	/**
	 * Esegue un comando UPDATE
	 * @param string $tabella la tabella da modificare
	 * @param array $valori un array contenente le coppie colonna => valore
	 * @param string $where [opz] il contenuto della formula WHERE
	 */
	public function update($tabella, $valori, $where='1') {
		if (!is_array($valori) || count($valori) == 0) return true;
		$sql = $this->updateSql($tabella, $valori, $where);
		Log::debug("SQL UPDATE", $sql);
		return $this->query($sql);
	}
	
	
	/**
	 * @param bool $chiudi [def:true] true per terminare il comando con ; 
	 * @param bool $completa [def:true] true per inserire la parte 'tabella SET'
	 * @see Database::update
	 * @return string
	 */
	protected function updateSql($tabella, $valori, $where=NULL, $chiudi=true, $completa=true) {
		$sql = 'UPDATE ';
		if ($completa) $sql.= "$tabella SET ";
		$primo = true;
		foreach($valori as $c => $v){
			if ($v === NULL) $v = "NULL";
			else $v = "'".$this->quote($v)."'";
			if ($primo) {
				$sql .= "$c = $v";
				$primo = false;
			} else {
				$sql .= ", $c = $v";
			}
		}
		if ($where !== NULL)
			$sql .= " WHERE $where";
		if ($chiudi) 
			$sql .= ';';
		return $sql;
	}
	
	/**
	 * Esegue un comando INSERT [...] ON DUPLICATE KEY UPDATE
	 * @param string $tabella il nome della tabella in cui inserire i valori
	 * @param array $valori un array contenente le coppie colonna => valore
	 * @param string $key [opz] le coppie colonna=>valore da usare solo nella INSERT
	 */
	public function insertUpdate($tabella, $valori, $key=NULL) {
		if (count($valori) == 0 && ($key === NULL || count($key) == 0))
			return true;
		if ($key === NULL)
			$insVal = $valori;
		else
			$insVal = $key+$valori;
		$in = $this->insertSql($tabella, $insVal, false);
		if (count($valori) > 0)
			$up = $this->updateSql($tabella, $valori, NULL, true, false);
		else {
			reset($key);
			$k = key($key);
			$up = "UPDATE $k=$k;";
		}
		return $this->query("$in ON DUPLICATE KEY $up");
	}
		
	/**
	 * Esegue un comando DELETE
	 * @param string $tabella la tabella da cui eliminare
	 * @param string $where il contenuto della formula WHERE
	 */
	public function delete($tabella, $where) {
		$query = "DELETE FROM $tabella WHERE $where;";
		Log::debug("SQL DELETE", $query);
		return $this->query($query);
	}

	/**
	 * Restituisce l'ultimo id inserito nel database
	 */
	public function lastId() {
		return $this->conn->insert_id;
	}

	/**
	 * Restituisce l'ultimo id inserito nel database
	 */
	public function affectedRows() {
		return $this->conn->affected_rows;
	}
	
	/**
	 * Esegue il quoting dei caratteri non permessi in una stringa sql
	 * @param string $string la stringa originale
	 * @return string la stringa quotata
	 */
	public function quote($string) {
		if ($string === NULL) return NULL;
		return $this->conn->real_escape_string($string);
	}
	
	/**
	 * Esegue il quoting dei caratteri non permessi in una stringa sql 
	 * utilizzata con l'operatore LIKE
	 * @param string $string la stringa originale
	 * @return string la stringa quotata
	 */
	public function quoteLike($string) {
		if ($string === NULL) return NULL;
		$string = $this->conn->real_escape_string($string);
		return addcslashes($string, '%_');
	}
	
	/**
	 * Esegue il quoting dei valori in un array e li compatta
	 * in una stringa del tipo<br>
	 * <code>('val1', 'val2', 'val3')</code>
	 * @param array $array
	 * @return string
	 */
	public function quoteArray($array) {
		if (!is_array($array) || count($array) == 0) return '()';
		foreach ($array as $v) {
			if ($v === NULL) $res[] = "NULL";
			else $res[] = "'".$this->quote($v)."'";
		}
		return '('.implode(', ', $res).')';
	} 
}
?>