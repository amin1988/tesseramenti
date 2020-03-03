<?php
if (!defined("_BASE_DIR_")) exit();

/**
 * @access public
 */
class Auth {
	const DURATA_SESS = 36000; //10 ore
	/**
	 * Chiave per id utente in sessione
	 * aggiornare anche ajax/sess.php
	 */
	const SESS_UID = 'tess_uid';
	/**
	 * Chiave per tipo utente in sessione
	 */
	const SESS_UTYPE = 'tess_user_type';
	/**
	 * Chiave oper l'ID società, NULL se l'utente non è società
	 */
	const SESS_IDSOC = 'tess_idsoc';
	
	const SESS_EMAIL = 'tess_email_segnala';
	
	const SESS_SCAD = 'tess_scadenza';
	
	const SESS_MASK = 'tess_mask';
	
	/**
	 * Login effettuato correttamente
	 * @var int
	 */
	const OK = 0;
	/**
	 * Utente non esistente
	 * @var int
	 */
	const NO_USER = 1;
	/**
	 * Password errata
	 * @var int
	 */
	const ERR_PSW = 2;
	
	private static $logged = NULL;
	private static $tipo = NULL;

	/**
	 * effettua il login e restituisce un intero 
	 * che indica il successo o il motivo del fallimento
	 * @param string user il nome utente
	 * @param string psw
	 * @return int
	 */
	public static function login($user, $psw) {
		include_model('Utente');
		
		$u = Utente::daNome($user);
		if ($u === NULL) return self::NO_USER;
		if (!$u->verificaPassword($psw))
			return self::ERR_PSW;
		self::initSess($u);
		Log::info('login ok',array('browser'=>$_SERVER['HTTP_USER_AGENT'],'ip'=>$_SERVER['REMOTE_ADDR']));
		return self::OK;
	}
	
	/**
	 * Inizializza le variabili per il login
	 * @param Utente $u
	 */
	private function initSess($u) {
		self::$logged = $u;
		self::$tipo = $u->getTipo();
		$_SESSION[self::SESS_UID] = $u->getId();
		$_SESSION[self::SESS_UTYPE] = $u->getTipo();
		if ($u->getTipo() == UTENTE_SOC)
			$_SESSION[self::SESS_IDSOC] = $u->getIDSocieta();
		else
			$_SESSION[self::SESS_IDSOC] = NULL;
		$_SESSION[self::SESS_SCAD] = time() + self::DURATA_SESS;
		$_SESSION[self::SESS_EMAIL] = $u->getEmail();
	}
	
	/**
	 * Effettua un accesso mascherato
	 * @param int $idsoc l'id della società da impersonare
	 */
	public static function loginAs($idsoc) {
		$ur = self::getUtente();
		if ($ur === NULL || $ur->getTipoReale() == UTENTE_SOC)
			return;
		$soc = new Societa($idsoc);
		if (!$soc->esiste())
			return;
		self::loginAsInner($ur->getId(), $soc->getId());
		Log::info('login mascherato societa', array('idsoc'=>$idsoc, 'nome'=>$soc->getNome()));
	}
	
	public static function loginAsSegr() {
		$ur = self::getUtente();
		if ($ur === NULL || $ur->getTipoReale() != UTENTE_ADMIN)
			return;
		self::loginAsInner($ur->getId());
		Log::info('login mascherato segreteria');
	}
	
	private function loginAsInner($idr, $idsoc=-1) {
		include_model('UtenteMask');
		$um = new UtenteMask($idr, $idsoc);
		self::initSess($um);
		$_SESSION[self::SESS_MASK] = $idsoc;
	}
	
	/**
	 * Indica se l'utente loggato è mascherato
	 * @return bool
	 */
	public static function isMascherato() {
		return isset($_SESSION[self::SESS_MASK]);
	}

	/**
	 * effettua il logout dell'utente loggato,
	 * o torna all'utente reale se era mascherato
	 * @access public
	 */
	public static function logout() {
		if (self::isMascherato()) {
			//torna se stesso
			include_model('Utente');
			$u = new Utente(self::getUtente()->getId());
			self::initSess($u);
			Log::info('logout mascherato', $_SESSION[self::SESS_MASK]);
			unset($_SESSION[self::SESS_MASK]);
		} else {
			self::logoutCompleto();
		}
		unset($_SESSION[self::SESS_MASK]);
	}
	
	/**
	 * Effettua il logout dell'utente
	 */
	private function logoutCompleto() {
		unset($_SESSION[self::SESS_UID]);
		unset($_SESSION[self::SESS_UTYPE]);
		unset($_SESSION[self::SESS_EMAIL]);
		unset($_SESSION[self::SESS_SCAD]);
		unset($_SESSION[self::SESS_MASK]);
		unset($_SESSION[self::SESS_IDSOC]);
		self::$logged = NULL;
		self::$tipo = NULL;
	}

	/**
	 * restituisce l'utente loggato o NULL se non è stato effettuato il login
	 * @return Utente
	 */
	public static function getUtente() {
		if (self::$logged === NULL && isset($_SESSION[self::SESS_UID])) {
			if (self::scaduto()) return NULL;
			include_model('Utente');
				
			if (self::isMascherato()) {
				include_model('UtenteMask');
				$u = new UtenteMask($_SESSION[self::SESS_UID], $_SESSION[self::SESS_MASK]);
			} else
				$u = new Utente($_SESSION[self::SESS_UID]);
			
			if ($u->esiste()) {
				self::$logged = $u;
			} else {
				self::logout();
			}
		}
		return self::$logged;
	}
	
	public static function getTipoUtente() {
		if (self::$tipo === NULL) {
			if (isset($_SESSION[self::SESS_UTYPE]) && !self::scaduto()) {
				self::$tipo = $_SESSION[self::SESS_UTYPE];
			} else
				self::$tipo = UTENTE_NO;
		}
		return self::$tipo;
	}
	
	public static function getEmailSegnalazione() {
		if (isset($_SESSION[self::SESS_EMAIL]))
			return $_SESSION[self::SESS_EMAIL];
		else
			return NULL;
	}
	
	public static function setEmailSegnalazione($email) {
		$_SESSION[self::SESS_EMAIL] = $email;
	}
	
	/**
	 * Rispistina lo stato. Usare per i test
	 */
	public static function reset() {
		self::$logged = NULL;
	}
	
	private function scaduto() {
		if (time() > $_SESSION[self::SESS_SCAD]) {
			self::logoutCompleto();
			return true;
		} else 
			return false;
		
	}
}
?>