<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Consiglio');
include_controller('LettoreAcsi');
include_formview('FormView');

class LettoreAcsi extends ViewWithForm {
	private $ctrl;
	private $dettagli;
	
	public function __construct($dettagli) {
		$this->dettagli = $dettagli;
		$this->ctrl = new LettoreAcsiCtrl();
		$f = $this->ctrl->getForm();
		$f->addSubmit('Carica file');
		$this->form = new FormView($f);
	}
	
	public function stampa() {
		$fv = $this->form;
		
		$anno = date('Y');
// 		$url = "http://www.acsionline.org/gestione/tesserati_lista_csv.php?stato=accettato&denominazione=&cognome=&nome=&numtessera=&numtessera_range_da=&numtessera_range_a=&tipo=&sesso=&giorno=&mese=&anno=&giorno2=&mese2=&anno2=&annualita=$anno&assicurazione=&disciplina=&provenienza=&Submit=Cerca";
		$url = "https://www.acsionline.org/#enrolled_search";
		echo "Una volta eseguito l'accesso sul sito ACSI, cliccare sul menù in alto sulla voce \"Tesseramenti\" e quindi cliccare \"Ricerca Tesserati\"<br>";
		echo "<a href=\"$url\" target=\"_blank\">Scarica file dal sito ACSI</a><br>";
// 		if(in_rinnovo())
// 		{
// 			$anno++;
// 			$url = "http://www.acsionline.org/gestione/tesserati_lista_csv.php?stato=accettato&denominazione=&cognome=&nome=&numtessera=&numtessera_range_da=&numtessera_range_a=&tipo=&sesso=&giorno=&mese=&anno=&giorno2=&mese2=&anno2=&annualita=$anno&assicurazione=&disciplina=&provenienza=&Submit=Cerca";
// 			echo "<a href=\"$url\" target=\"_blank\">Scarica file dal sito ACSI - Anno $anno</a><br>";
// 		}
		$fv->stampaInizioForm();
		$fv->stampa(LettoreAcsiCtrl::F_FILE, NULL, array('value style'=>'margin-top: 5px;'));
		$fv->stampaSubmit(array('class'=>'btn-primary'));
		$fv->stampaFineForm();
		
		if ($this->ctrl->fileLetto()) {
			echo '<div class="alert alert-success">File caricato correttamente</div>';
		}
		
		$soclist = $this->ctrl->getElencoSocieta();
		uasort($soclist, array('Societa','compareFull'));
		$tot = 0;
		foreach ($soclist as $idsoc=>$soc) {
			$tot += count($this->ctrl->getTesserati($idsoc));
		}
		
		echo '<h3>Tesserati da confermare: '.$tot.'</h3><ul>';
		foreach ($soclist as $idsoc => $soc) {
			$soc = $this->ctrl->getSocieta($idsoc);
			echo '<li><b>'.$soc->getNome().'</b><ol>';
			$et = $this->ctrl->getTesserati($idsoc);
			uasort($et, array('Tesserato','compare'));
			foreach ($et as $idtess => $tess) {
				/* @var $tess Tesserato */
				$url = call_user_func($this->dettagli, $idtess);
				echo '<li>';
				echo $this->ctrl->getNumTessera($idsoc, $idtess).' - ';
				
				if ($tess->getIndirizzo() == ''
						|| $tess->getCap() == ''
						|| $tess->getCittaRes() == ''
						|| $tess->getIDProvinciaRes() == 0)
				{
					echo '<i class="icon-ban-circle" title="DATI INCOMPLETI"></i> ';
				} 
				
				echo "<a href=\"$url\" target=\"_blank\">";
				echo $tess->getCognome().' '.$tess->getNome().'</a>';
				
				/*
				if(Consiglio::isMembro($tess->getId()))
				{
					$url = $this->campo($tess->getCognome(), $tess->getNome());
					echo " <a href=\"$url\" target=\"_blank\" class=\"btn btn-warning\">Conferma sul sito ACSI</a>";
				}
				*/	
					
				echo'</li>';
			}
			echo '</ol>';
		}
		echo '</ul>';
	}
	
	public function campo($cog, $nom)
	{
		$cog = strtolower($cog);
		$nom = strtolower($nom);
		
		$anno = date('Y');
		if(in_rinnovo())
			$anno++;
		
		return "http://www.acsionline.org/gestione/tesserati_lista.php?stato=accettato&denominazione=&cognome=$cog&nome=$nom&numtessera=&numtessera_range_da=&numtessera_range_a=&tipo=&sesso=&giorno=&mese=&anno=&giorno2=&mese2=&anno2=&annualita=$anno&assicurazione=&disciplina=&provenienza=&Submit=Cerca";
	}
	
}