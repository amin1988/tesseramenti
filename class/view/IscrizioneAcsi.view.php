<?php
if (!defined('_BASE_DIR_')) exit();
include_formview('FormView');
include_controller('FileIscrizioneAcsi');

include_form('Form');
include_form(FORMELEM_CHECK);

class IscrizioneAcsi extends ViewWithForm {
	const F_NUOVI = 'n';
	const F_ATTESA = 'w';
	
	private $filegen;
	private $ctrl;
	private $dettagli;
	
	/**
	 * 
	 * @param string $filegen pagina per il download del file
	 * @param callable $dettagli funzione che prende id tesserato e restituisce url dei dettagli
	 */
	public function __construct($filegen, $dettagli) {
		$this->ctrl = new FileIscrizioneAcsiCtrl(in_rinnovo(), true, true);
		$this->filegen = $filegen;
		$this->dettagli = $dettagli;
		
		$f = new Form('fileopt', false, false);
		$ntess = $this->ctrl->tessereDisponibili();
		$el = new FormElem_Check(self::F_NUOVI, $f, NULL, $ntess > 0);
		if ($ntess == 0) $el->setDisabilitato(true);
		new FormElem_Check(self::F_ATTESA, $f, NULL, $ntess == 0);
		$f->addSubmit('Genera file');
		$this->form = new FormView($f);
	}
	
	public function stampa() {
		$nuovi = array();
		$attesa = array();
		
		$totnuovi = 0;
		$totatt = 0;
		
		$soc = $this->ctrl->getElencoSocieta();
		uasort($soc, array('Societa','compareFull'));
		foreach ($soc as $idsoc=>$soc) {
			foreach ($this->ctrl->getTesserati($idsoc) as $idtess => $tess) {
				if ($this->ctrl->isInviato($idtess)) {
					$attesa[$idsoc][$idtess] = $tess;
					$totatt++;
				} else {
					$nuovi[$idsoc][$idtess] = $tess;
					$totnuovi++;
				}
			}
		}
		
		$fv = $this->form;
		
		$ntess = $this->ctrl->tessereDisponibili();
		if ($ntess == 0)
			$alert = 'alert-error';
		else if ($ntess < 50)
			$alert = '';
		else 
			$alert = 'alert-info';
		
		echo "<div class=\"alert $alert\"><strong>Tessere disponibili: $ntess</strong></div>\n";
		
		$fv->stampaInizioForm(array('target'=>'_blank', 'action'=>$this->filegen));
		$eln = $fv->getElem(self::F_NUOVI);
		echo '<label class="checkbox';
		if ($eln->getFormElem()->isDisabilitato()) echo ' muted';
		echo '">';
		$eln->stampa(NULL);
		echo " Includi tesserati non assicurati</label>\n<label class=\"checkbox\">";
		$fv->stampa(self::F_ATTESA); 
		echo " Includi tesserati in attesa di conferma</label>\n";
		$fv->stampaSubmit(array('class'=>'btn-primary'));
		$fv->stampaFineForm();
		
		echo '<ul class="nav nav-tabs"><li class="active"><a href="#nonass" data-toggle="tab">Non assicurati ';
		echo "<span class=\"badge\">$totnuovi</span></a></li>\n";
		echo '<li><a href="#attesa" data-toggle="tab">Da confermare ';
		echo "<span class=\"badge\">$totatt</span></a></li>\n</ul>\n";
		echo '<div class="tab-content">';
		$this->stampaElencoTess($nuovi, 'nonass', true, false);
		$this->stampaElencoTess($attesa, 'attesa', false, true);
		echo '</div>';
	}
	
	/**
	 * 
	 * @param Tesserato[][] $elenco formato idsocieta => idtesserato => Tesserato
	 * @param boolean $num [def:false] true per stampare anche il numero tessera
	 */
	private function stampaElencoTess($elenco, $id, $active, $num=false) {
		if ($active) $clactive = 'active';
		else $clactive = '';
		echo "	\n<div id=\"$id\" class=\"tab-pane $clactive\"><ul>\n";
		foreach ($elenco as $idsoc => $et) {
			$soc = $this->ctrl->getSocieta($idsoc);
			echo '<li><b>'.$soc->getNome().'</b><ol>';
			uasort($et, array('Tesserato','compare'));
			foreach ($et as $idtess => $tess) {
				/* @var $tess Tesserato */
				$url = call_user_func($this->dettagli, $idtess);
				echo '<li>';
				if ($num)
					echo $this->ctrl->getNumTessera($idsoc, $idtess).' - ';
				
				if ($tess->getLuogoNascita() == ''
						|| $tess->getIndirizzo() == ''
						|| $tess->getCap() == ''
						|| $tess->getCittaRes() == ''
						|| $tess->getIDProvinciaRes() == 0
						|| $tess->getCodiceFiscale() === NULL
						|| $tess->getCodiceFiscale() == '')
				{
					echo '<i class="icon-ban-circle" title="DATI INCOMPLETI"></i> ';
				} 
				
				echo "<a href=\"$url\" target=\"_blank\">";
				echo $tess->getCognome().' '.$tess->getNome()."</a></li>\n";
			}
			echo '</ol>';
		}
		echo '</ul></div>';
	}
	
}