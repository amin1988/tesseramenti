<?php
if (!defined('_BASE_DIR_')) exit();
include_controller('Rinnovo');

class Rinnovo { 
	private $ctrl;
	private $inner;
	private $func_urlFase;
	
	/**
	 * @param int $ids id società
	 * @param int $fase fase di rinnovo
	 * @param callable $func_urlFase funzione che restituisce l'url relativo della pagina
	 * di una certa fase. Parametro: $fase
	 */
	public function __construct($ids, $fase, $func_urlFase) {
		$this->ctrl = new RinnovoCtrl($ids, $fase);
		$this->func_urlFase = $func_urlFase;
		$submit = 'Avanti &gt;';
		$callback = array($this, 'gotoProx');
		switch ($this->ctrl->getFase()) {
			case RinnovoCtrl::FASE_SOC:
				include_view('ModificaSocieta');
				$this->inner = new ModificaSocieta($ids, false, $callback, false);
				break;
			case RinnovoCtrl::FASE_SETT:
				include_view('ModificaSettori');
				$this->inner = new ModificaSettori($ids, true, $callback, false);
				break;
			case RinnovoCtrl::FASE_CONS:
				include_view('ModificaConsiglio');
				$this->inner = new ModificaConsiglio($ids, $callback, false);
				break;
			case RinnovoCtrl::FASE_TESS_1:
				include_view('RinnovaTesserati');
				$this->inner = new RinnovaTesserati($ids, true, $callback, false, 1);
				break;
			case RinnovoCtrl::FASE_TESS_2:
				include_view('RinnovaTesserati');
				$this->inner = new RinnovaTesserati($ids, true, $callback, false, 2);
				break;
			case RinnovoCtrl::FASE_TESS_3:
				include_view('RinnovaTesserati');
				$this->inner = new RinnovaTesserati($ids, true, $callback, false, 3);
				break;
			case RinnovoCtrl::FASE_TESS_4:
				include_view('RinnovaTesserati');
				$this->inner = new RinnovaTesserati($ids, true, $callback, false, 4);
				break;
			case RinnovoCtrl::FASE_TESS_5:
				include_view('RinnovaTesserati');
				$this->inner = new RinnovaTesserati($ids, true, $callback, false, 5);
				break;
			case RinnovoCtrl::FASE_TESS_6:
				include_view('RinnovaTesserati');
				$this->inner = new RinnovaTesserati($ids, true, $callback, false, 6);
				$submit = 'Fine &gt;';
				break;
		}
		if ($this->inner !== NULL)
			$this->inner->getForm()->getForm()->addSubmit($submit);
	}
	
	public function gotoProx() {
		redirect(call_user_func($this->func_urlFase, $this->ctrl->getFaseSucc()));
	}
	
	function getSubview() {
		return $this->inner;
	}	

	public function stampa() {
		//header
		echo '<ul class="breadcrumb">';
		$fasi = array(
				RinnovoCtrl::FASE_SOC => 'Dati società',
				RinnovoCtrl::FASE_SETT => 'Settori',
				RinnovoCtrl::FASE_CONS => 'Consiglio',
				RinnovoCtrl::FASE_TESS_1 => 'Tesserati A-B',
				RinnovoCtrl::FASE_TESS_2 => 'Tesserati C-D',
				RinnovoCtrl::FASE_TESS_3 => 'Tesserati E-I',
				RinnovoCtrl::FASE_TESS_4 => 'Tesserati J-M',
				RinnovoCtrl::FASE_TESS_5 => 'Tesserati N-R',
				RinnovoCtrl::FASE_TESS_6 => 'Tesserati S-Z',
			);
		foreach ($fasi as $fase => $nome) {
			echo '<li>';
			$attuale = ($fase == $this->ctrl->getFase());
			if ($attuale)
				echo '<strong>';
			$attiva = !$attuale && $this->ctrl->faseAttiva($fase);
			if ($attiva) {
				$url = _PATH_ROOT_.call_user_func($this->func_urlFase, $fase);
				echo "<a href=\"$url\">";
			}
			echo $nome;
			if ($attiva)
				echo '</a>';
			if ($attuale)
				echo '</strong>';
			if ($fase != RinnovoCtrl::FASE_TESS_6)
				echo ' <span class="divider"><i class="icon-chevron-right"></i></span>';
			echo "</li>\n";	
		}
		echo "</ul>\n\n";
		
		//inner
		if ($this->inner !== NULL)
			$this->inner->stampa();
		else 
			echo '<div class="alert alert-success"><h3 class="text-center">Rinnovo completato</h3></div>';
		
		//footer
		echo '<div class="form-actions">';
		$f = $this->ctrl->getFasePrec();
		if ($f !== NULL) {
			$url = _PATH_ROOT_.call_user_func($this->func_urlFase, $f);
			echo "<div class=\"pull-left\"><a href=\"$url\" class=\"btn\">&lt; Indietro</a></div>\n";
		}
		
		if ($this->inner !== NULL) {
			$fv = $this->inner->getForm();
			echo '<div class="pull-right">';
			$fv->stampaSubmit();
			echo "</div>\n</div>\n\n";
			$fv->stampaFineForm();
		} else 
			echo "</div>\n\n";
	}
}
