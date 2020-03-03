<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Modello');

class Segnalazione extends Modello {
	
	public static function crea($pagina, $desc, $email) {
		$s = new Segnalazione();
		$ut = Auth::getUtente();
		if ($ut !== NULL)
			$s->set('idutente', $ut->getId());
		$s->set('pagina', $pagina);
		$s->set('browser', $_SERVER['HTTP_USER_AGENT']);
		$s->set('descrizione', $desc);
		$s->set('email', $email);
		return $s;
	}
	
	public function __construct($id = NULL) {
		parent::__construct('segnalazioni', 'idsegnalazione', $id);
	}
	
	/**
	 * @return int
	 */
	public function getIdUtente() {
		return $this->get('idutente');
	}
	
	/**
	 * @return string
	 */
	public function getPagina() {
		return $this->get('pagina');
	}
	
	/**
	 * @return string
	 */
	public function getBrowser() {
		return $this->get('browser');
	}
	
	/**
	 * @return string
	 */
	public function getDescrizione() {
		return $this->get('descrizione');
	}
}

?>