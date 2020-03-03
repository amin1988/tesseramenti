<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Tesserato');
include_class('Sesso');

/**
 * Nessun errore nel codice fiscale
 */
define('CODFIS_OK','ok');
/**
 * Cognome errato nel codice fiscale
 */
define('CODFIS_COGNOME','cog');
/**
 * Nome errato nel codice fiscale
 */
define('CODFIS_NOME','nome');
/**
 * Anno errato nel codice fiscale
 */
define('CODFIS_ANNO','anno');
/**
 * Mese errato nel codice fiscale
 */
define('CODFIS_MESE','mese');
/**
 * Giorno errato nel codice fiscale
 */
define('CODFIS_GIORNO','gior');
/**
 * Sesso errato nel codice fiscale
 */
define('CODFIS_SESSO','sesso');
/**
 * Luogo di nascita errato nel codice fiscale
 */
define('CODFIS_LUOGO','luo');
/**
 * Codice di controllo errato nel codice fiscale
 */
define('CODFIS_CTRL','ctrl');
/**
 * Lunghezza errata del codice fiscale
 */
define('CODFIS_LUNGH','len');

class CodiceFiscale {
	private static $ctrl = NULL;
	private static $acc = NULL;
	
	/**
	 * Verifica che il codice fiscale di un tesserato sia corretto
	 * @param Tesserato $t
	 * @return CODFIS_OK se non ci sono errori, altrimenti un'altra delle costanti CODFIS_*
	 */
	static function verifica($t) {
		$cod = self::normalizza($t->getCodiceFiscale());
		if ($cod === NULL) return CODFIS_OK;
		if (strlen($cod) != 16) return CODFIS_LUNGH;
		
		$gen  = self::cognome($t->getCognome());
		$gen .= self::nome($t->getNome());
		$gen .= self::nascita($t->getDataNascita(), $t->getSesso() == Sesso::M);
		$gen .= self::luogo($t->getLuogoNascita());
		
		$c = self::confronta($cod, $gen);
		if ($c === true) {
			if (self::carattereControllo($cod) == $cod[15])
				return CODFIS_OK;
			else
				return CODFIS_CTRL;
		} else {
			if ($c < 3) return CODFIS_COGNOME;
			if ($c < 6) return CODFIS_NOME;
			if ($c < 8) return CODFIS_ANNO;
			if ($c == 8) return CODFIS_MESE;
			if ($c == 9) {
				$n = $cod[9];
				if ($n > 7) return CODFIS_GIORNO;
				$u = $t->getSesso() == Sesso::M;
				if (($u && $n>3)||(!$u && $n<4)) return CODFIS_SESSO;
				return CODFIS_GIORNO;
			}
			if ($c == 10) return CODFIS_GIORNO;
			return CODFIS_LUOGO;
		}
	}
	
	/**
	 * Indica se il codice inserito è equivalente a quello calcolato
	 * @param string $input il codice inserito normalizzato 
	 * @param string $calc il codice calcolato
	 * @return boolean true se sono uguali, oppure l'indice della prima differenza
	 */
	private static function confronta($input, $calc) {
		for($i=0; $i<15; $i++) {
			if ($input[$i] != $calc[$i] && $calc[$i] != '?') {
				if (!is_numeric($calc[$i])) return $i;
				switch ($calc[$i]) {
					case 0:
						$o = 'L';
						break;
					case 1:
						$o = 'M';
						break;
					case 2:
						$o = 'N';
						break;
					case 3:
						$o = 'P';
						break;
					case 4:
						$o = 'Q';
						break;
					case 5:
						$o = 'R';
						break;
					case 6:
						$o = 'S';
						break;
					case 7:
						$o = 'T';
						break;
					case 8:
						$o = 'U';
						break;
					case 9:
						$o = 'V';
						break;
				}
				if ($input[$i] != $o) return $i;
			}
		}
		return true;
	}
	
	/**
	 * Trasforma il codice eliminando gli spazi e mettendolo maiuscolo
	 * @param string $codfis il codice da normalizzare
	 * @return il codice normalizzato
	 */
	static function normalizza($codfis) {
		if ($codfis === NULL || $codfis == '') return NULL;//TODO controllare
		if (self::$acc === NULL) self::init();
		$codfis = strtr($codfis, self::$acc);
		return strtoupper(preg_replace('/[^0-9a-z]/i', '', $codfis));
	}
	
	/**
	 * Restituisce il carattere di controllo 
	 * @param string $cod il codice fiscale completo o mancante del codice di controllo
	 * @return char il carattere corretto
	 */
	static function carattereControllo($cod) {
		$len = strlen($cod);
		if ($len > 16 || $len < 15) return NULL;
		if (self::$ctrl === NULL) self::init();
		
		$tot=0;
		for($i=0; $i<15; $i++) {
			if ($cod[$i] == '?') return '?';
			$tot += self::$ctrl[$i%2][$cod[$i]];
		}
		return self::$ctrl['r'][$tot%26];
	} 

	/**
	 * Genera i caratteri relativi al cognome
	 * @param string $nome il cognome completo
	 */
	static function cognome($cognome) {
		if ($cognome === NULL) return '???';
		self::separaVocali($cognome, $voc, $cons);
		$nc = count($cons);
		if ($nc >= 3) return $cons[0].$cons[1].$cons[2];
		$r = '';
		for($i = 0; $i<$nc; $i++)
			$r .= $cons[$i];
		for($i=0; $i<3-$nc && $i<count($voc); $i++)
			$r .= $voc[$i];
		if ($nc + count($voc) >= 3)
			return $r;
		else
			return $r .= str_repeat('X', 3 - $nc - count($voc));
	}

	/**
	 * Genera i caratteri relativi al nome
	 * @param string $nome il nome completo
	 */
	static function nome($nome) {
		if ($nome === NULL) return '???';
		self::separaVocali($nome, $voc, $cons);
		$nc = count($cons);
		if ($nc >= 4) return $cons[0].$cons[2].$cons[3];
		if ($nc == 3) return $cons[0].$cons[1].$cons[2];
		$r = '';
		for($i = 0; $i<$nc; $i++)
			$r .= $cons[$i];
		for($i=0; $i<3-$nc && $i<count($voc); $i++)
			$r .= $voc[$i];
		if ($nc + count($voc) >= 3)
			return $r;
		else
			return $r .= str_repeat('X', 3 - $nc - count($voc));
	}
	
	/**
	 * Separa le vocali dalle consonanti
	 * @param string $str la parola da analizzare
	 * @param string[] $voc le singole vocali in ordine
	 * @param string[] $cons le singole consonanti in ordine
	 */
	private static function separaVocali($str, &$voc, &$cons) {
		$voc = array();
		$cons = array();
		$str = self::normalizza($str);
		$len = strlen($str);
		for($i = 0; $i<$len; $i++) {
			if (self::isVocale($str[$i]))
				$voc[] = $str[$i];
			else
				$cons[] = $str[$i]; 
		}
	}

	private static function isVocale($c) {
		return stripos('AEIOU',$c) !== false;
	}
	
	/**
	 * Genera i caratteri relativi alla data di nascita
	 * @param Data $data la data di nascita
	 * @param boolean $uomo true se la data è relativa ad un uomo,
	 * false se è relativa ad una donna
	 */
	static function nascita($data, $uomo) {
		if ($data === NULL) return '?????';
		$anno = $data->getAnno()%100;
		$giorno = $data->getGiorno();
		if (!$uomo) $giorno += 40;
		switch ($data->getMese()) {
			case 1:
				$mese = 'A';
				break;
			case 2:
				$mese = 'B';
				break;
			case 3:
				$mese = 'C';
				break;
			case 4:
				$mese = 'D';
				break;
			case 5:
				$mese = 'E';
				break;
			case 6:
				$mese = 'H';
				break;
			case 7:
				$mese = 'L';
				break;
			case 8:
				$mese = 'M';
				break;
			case 9:
				$mese = 'P';
				break;
			case 10:
				$mese = 'R';
				break;
			case 11:
				$mese = 'S';
				break;
			case 12:
				$mese = 'T';
				break;
		}
		if ($uomo === NULL)
			return sprintf('%02d%s?%d', $anno, $mese, $giorno%10);
		else
			return sprintf('%02d%s%02d', $anno, $mese, $giorno);
	}
	
	static function luogo($luogo) {
		return '????';
	} 
	
	private static function init() {
		//accenti
		self::$acc = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
				'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
				'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
				'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
				'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
		
		//pari
		self::$ctrl[0] = array(
				'0' => 1,
				'1' => 0,
				'2' => 5,
				'3' => 7,
				'4' => 9,
				'5' => 13,
				'6' => 15,
				'7' => 17,
				'8' => 19,
				'9' => 21,
				'A' => 1,
				'B' => 0,
				'C' => 5,
				'D' => 7,
				'E' => 9,
				'F' => 13,
				'G' => 15,
				'H' => 17,
				'I' => 19,
				'J' => 21,
				'K' => 2,
				'L' => 4,
				'M' => 18,
				'N' => 20,
				'O' => 11,
				'P' => 3,
				'Q' => 6,
				'R' => 8,
				'S' => 12,
				'T' => 14,
				'U' => 16,
				'V' => 10,
				'W' => 22,
				'X' => 25,
				'Y' => 24,
				'Z' => 23
		);
		
		//dispari
		self::$ctrl[1] = array(
					'0' => 0,
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
					'7' => 7,
					'8' => 8,
					'9' => 9,
					'A' => 0,
					'B' => 1,
					'C' => 2,
					'D' => 3,
					'E' => 4,
					'F' => 5,
					'G' => 6,
					'H' => 7,
					'I' => 8,
					'J' => 9,
					'K' => 10,
					'L' => 11,
					'M' => 12,
					'N' => 13,
					'O' => 14,
					'P' => 15,
					'Q' => 16,
					'R' => 17,
					'S' => 18,
					'T' => 19,
					'U' => 20,
					'V' => 21,
					'W' => 22,
					'X' => 23,
					'Y' => 24,
					'Z' => 25
				);
		
		self::$ctrl['r'] = array(
					0 => 'A',
					1 => 'B',
					2 => 'C',
					3 => 'D',
					4 => 'E',
					5 => 'F',
					6 => 'G',
					7 => 'H',
					8 => 'I',
					9 => 'J',
					10 => 'K',
					11 => 'L',
					12 => 'M',
					13 => 'N',
					14 => 'O',
					15 => 'P',
					16 => 'Q',
					17 => 'R',
					18 => 'S',
					19 => 'T',
					20 => 'U',
					21 => 'V',
					22 => 'W',
					23 => 'X',
					24 => 'Y',
					25 => 'Z',
				);
	}
}