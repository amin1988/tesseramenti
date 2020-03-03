<?php
if (!defined("_BASE_DIR_")) exit();

class Sesso {
	const M = 1;
	const F = 2;
	const MISTO = 3;
	
	public static function getValoriLunghi($misto=false) {
		$ret[self::M] = self::toStringLungo(self::M);
		$ret[self::F] = self::toStringLungo(self::F);
		if ($misto)
			$ret[self::MISTO] = self::toStringLungo(self::MISTO);
		return $ret;
	}
	
	public static function toStringBreve($idsesso) {
		switch ($idsesso) {
			case self::M:
				return "M";
			case self::F:
				return "F";
			case self::MISTO:
				return "M-F";
		}
	}
	
	public static function toStringLungo($idsesso) {
			switch ($idsesso) {
			case self::M:
				return "Maschile";
			case self::F:
				return "Femminile";
			case self::MISTO:
				return "Misto";
		}
	} 
}
?>