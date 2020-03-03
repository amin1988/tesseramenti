<?php
if (!defined('_BASE_DIR_')) exit();

abstract class DatiExtra {
	protected $dati = array();
	protected $mod = array();
	
	public function get($key) {
		if (array_key_exists($key, $this->dati))
			return $this->dati[$key];
		else
			return $this->carica($key); 
	}
	
	public function set($key, $val) {
		if (!array_key_exists($key, $this->dati)
				|| $this->dati[$key] != $val)
		{
			$this->dati[$key] = $val;
			$this->mod[$key] = true;
		}
	}
	
	public abstract function getChiavi();
	public abstract function getValori($key);
	public abstract function toString($key);
	public abstract function salva();
	
	/**
	 * Carica il valore di un dato in $dati e lo restituisce
	 * @param string $key la chiave del dato da caricare
	 * @return mixed
	 */
	protected abstract function carica($key);
	public abstract function elimina($key=NULL);
	
}