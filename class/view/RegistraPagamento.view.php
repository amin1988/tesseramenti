<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Settore','Tipo','Tesserato');
include_controller('RegistraPagamento');
include_formview('FormView');
include_view('WaitingView');

class RegistraPagamento extends ViewWithForm {
	const F_SUBMIT = 'invia';
	
	private $ctrl;
	private $wait;
	
	public function __construct($idsoc) {
		$this->ctrl = new RegistraPagamentiCtrl($idsoc);
		$f = $this->ctrl->getForm();
		new FormElem_Submit('Inserisci pagamento', $f, self::F_SUBMIT);
		$this->form = new FormView($f);
		$this->wait = new WaitingView(array($this, 'stampaInner'));
	}
	
	public function getSubview() {
		return array($this->form, $this->wait);
	}
	
	public function getJsInclude() {
		return array('registra_pag');
	}
	
	public function stampaJsOnload() {
		foreach ($this->ctrl->getErrori() as $err) {
			switch ($err) {
				case RegistraPagamentiCtrl::ERR_OBBLIG:
					echo "$('#err_obblig').show();\n";
					break;
				case RegistraPagamentiCtrl::ERR_FORMATO:
					echo "$('#err_format').show();\n";
					break;
				case RegistraPagamentiCtrl::ERR_SEL:
					echo "$('#err_sel').show();\n";
					break;
				case RegistraPagamentiCtrl::ERR_SALVA:
					echo "$('#err_salva').show();\n";
					break;
			}			
		}
		$tot = $this->ctrl->getPrezzoTotale();
		echo "registra_pag.init($tot);\n";
	}
	
	public function stampa() {
		$this->wait->stampa();
	}
	
	public function stampaInner() {
		$fv = $this->form;
		$fv->stampaInizioForm();
		
		//intestazione
		echo '<div class="row"><div class="span6 text-right"><strong>Pagamento ricevuto:</strong></div>';
		echo '<div class="span6">';
		$fv->stampa(FormPagamento::TOT, NULL, array('placeholder'=>'0,00'));
		echo ' &euro; ';
		echo '<span class="text-error err_msg" style="display:none;" id="err_obblig">Campo obbligatorio</span>';
		echo '<span class="text-error err_msg" style="display:none;" id="err_format">Valore non valido, inserire un numero</span>';
		echo '</div></div>';
		echo '<div class="row"><div class="span6 text-right"><strong>Selezionato:</strong></div>';
		echo '<div class="span6"><span id="totsel">0,00</span> &euro;</div></div>';
		echo '<div class="row"><div class="span6 text-right"><strong>Da selezionare:</strong></div>';
		echo '<div class="span6"><span id="totnonsel">0,00</span> &euro;</div></div>';
		echo '<div class="text-center" style="margin: 10px;">';
		$fv->stampa(self::F_SUBMIT);
		echo '</div><div id="err_sel" style="display:none;" class="err_msg alert alert-error"><strong>Errore:</strong> Il totale selezionato non corrisponde al pagamento ricevuto';
		echo '</div><div id="err_salva" style="display:none;" class="err_msg alert alert-error"><strong>Errore:</strong> Si &egrave; verificato un errore durante il salvataggio';
		echo '</div><div class="text-center" style="margin: 10px;">';
		echo '<button type="button" class="btn" onclick="registra_pag.selezionaTutto()">Seleziona tutto</button> ';
		echo '<button type="button" class="btn" onclick="registra_pag.deselezionaTutto()">Deseleziona tutto</button>';
		echo '</div>';
		
		//tabelle settori
		$sett = Settore::listaId($this->ctrl->getIDSettori());
		uasort($sett, array('Settore','compare'));
		foreach ($sett as $s) {
			$this->stampaSettore($s);
		}
		
		$fv->stampaFineForm();
	}
	
	/**
	 * @param Settore $sett
	 * @param FormView $fv
	 */
	private function stampaSettore($sett) {
		$fv = $this->form;
		
		//titolo settore
		$idsett = $sett->getId();
		echo '<div class="row"><div class="span8 text-left"><label class="checkbox lead">';
		$p = $this->ctrl->getPrezzoSettore($idsett);
		$fv->stampa(FormPagamento::SETTORE, $idsett, 
				array('class' => 'chk-prezzo chk-sett', 'data-prezzo' => $p, 'data-idsett' => $idsett));
		echo $sett->getNome().'</label></div>';
		echo '<div class="span2 text-right lead">Totale:</div>';
		echo "<div class=\"span2 lead\"><span id=\"totsett_$idsett\">0,00</span> &euro;</div></div>\n";
		
		//tabella tesserati
		echo "<div class=\"tab_sett\" id=\"tab_sett$idsett\">";
		$tess = $this->ctrl->getTesserati($idsett);
		if (count($tess) == 0)
			echo '<div class="well well-small">Nessun tesserato da pagare per questo settore</div>';
		else
			$this->stampaTabella($idsett, $tess);
		echo "</div>\n\n";
	}
	
	/**
	 * @param int $idsett
	 * @param Tesserato[] $tess
	 */
	private function stampaTabella($idsett, $tess) {
		uasort($tess, array('Tesserato','compare'));
		$fv = $this->form;
		$tipi = $this->ctrl->getIDTipi($idsett);
		
		echo '<table class="table table-striped table-hover table-condensed">';
		echo '<thead><tr><th>Tesserato</th>';
		foreach ($tipi as $idtipo) {
			$nome = Tipo::fromId($idtipo)->getNome();
			echo "<th><div class=\"text-right\">$nome</div></th>\n";
		}
		echo "<th><div class=\"text-right\">Totale</div></th></tr></thead>\n<tbody>";
		
		$key = array($idsett, 0); 
		foreach ($tess as $idtess => $t) {
			/* @var $t Tesserato */
			$ptot = $this->ctrl->getTotaleTesserato($idsett, $idtess);
			
			//check e nome
			echo '<tr><td><label class="checkbox">';
			$key[1] = $idtess;
			$fv->stampa(FormPagamento::TESS, $key, array(
					'class' => "chk-prezzo chk-tess chk-sett_$idsett",
					'data-idtess' => $idtess,
					'data-prezzo' => $ptot
			));
			echo ' '.$t->getCognome().' '.$t->getNome();
			echo '</label></td>';
			
			//tipi
			foreach ($tipi as $idtipo) {
				$p = $this->ctrl->getPrezzoTipo($idsett, $idtess, $idtipo);
				if ($p === NULL) 
					$p = '';
				else
					$p = $this->euro($p).' &euro;';
				echo "<td><div class=\"text-right\">$p</div></td>\n";
			}
			//totale
			echo "<td><div class=\"text-right\">".$this->euro($ptot)." &euro;</div></td></tr>\n";
		}
		echo "</tbody></table>\n";
	}
	
	private function euro($v) {
		return str_replace('.', ',', sprintf("%.2f", $v));
	}
	
}