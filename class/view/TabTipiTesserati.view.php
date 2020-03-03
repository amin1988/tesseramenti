<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Tipo','Settore');

class TabTipiTesserati {
	private $sett;
	private $tipo;
	private $func_taburl;
	
	/**
	 * 
	 * @param unknown $idsoc
	 * @param unknown $idtipo
	 * @param unknown $func_taburl callback a funzione che prende un id tipo e 
	 * restituisce l'url della pagina da mostrare
	 */
	public function __construct($idsoc, $idtipo, $func_taburl) {
		$this->tipo = Tipo::fromId($idtipo);
		$this->sett = Settore::fromId($this->tipo->getIDSettore());
		$this->func_taburl = $func_taburl;
	}
	
	function stampa() {
		echo '<h2>Settore '.$this->sett->getNome().'</h2>';	
		echo '<ul class="nav nav-tabs">';
		$selt = $this->tipo->getId();
		foreach (Tipo::getFromSettore($this->sett->getId()) as $idt => $tipo) {
			$url = call_user_func($this->func_taburl, $idt);
			echo '<li';
			if ($idt == $selt) echo ' class="active"';
			echo "><a href=\"$url\">";
			echo $tipo->getPlurale();
			echo '</a></li>';
		}
		echo '</ul>';
	}
}