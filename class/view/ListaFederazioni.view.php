<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Societa','Federazione');
include_class('Data');

class ListaFederazioni {
	private $callback;
	private $anno;
	
	public function __construct($callback, $anno)
	{
		$this->callback = $callback;
		$this->anno = $anno;
	}
	
	public function stampa()
	{
		$ar_fed = Federazione::elenco();
		
		foreach($ar_fed as $id_fed=>$fed)
		{
			if($id_fed == 1)
				continue;
			/*@var $fed Federazione */
			$nf = $fed->getNome();
			$anno = $this->anno;
			
			$url = call_user_func($this->callback, $fed);
			
			$somme = $this->getTotale($id_fed, $anno);
			$pagati = $this->euro($somme[0]['totale']/100);
			$tes_pag = $this->euro($somme[0]['tesseramento']/100);
			$aff_pag = $this->euro($somme[0]['settore']/100);
			$non_pag = $this->euro($somme[1]['totale']/100);
			$tot = $this->euro(($somme[0]['totale'] + $somme[1]['totale'])/100);
			
			echo '<div class="well attuale">';
			echo "<h4>Federazione: <a href=\"$url\">$nf</a></h4>";
			echo '<div class="pagamenti-totali"><div class="row-fluid">';
			echo '<div class="span6 tab-label">Totale:</div><div class="span6">'.$tot.'</div>';
			echo '</div><div class="row-fluid">';
			echo '<div class="span6 tab-label">Saldato:</div><div class="span6">'.$pagati.'</div>';
			echo '</div><div class="row-fluid">';
			echo '<div class="span3 tab-label">Affiliazione:</div><div class="span3">'.$aff_pag.'</div>';
			echo '<div class="span3 tab-label">Tesseramento:</div><div class="span3">'.$tes_pag.'</div>';
			echo '</div><div class="row-fluid">';
			echo '<div class="span6 tab-label">Da saldare:</div><div class="span6">'.$non_pag.'</div>';
			echo '</div></div></div>';
		}
	}
	
	public function euro($num) {
		return str_replace('.', ',', sprintf('&euro; %.2f', $num));
	}
	
	private function getTotale($id_fed, $anno)
	{
		$ar_soc = Societa::listaCompleta($id_fed);
		$pagati = array('settore'=>0,'tesseramento'=>0,'totale'=>0);
		$non_pag = array('settore'=>0,'tesseramento'=>0,'totale'=>0);
		
		foreach($ar_soc as $id_soc=>$soc)
		{
			$ar_pag = PagamentoUtil::get()->getPagati($id_soc, $anno);
			foreach($ar_pag as $id_p=>$pag)
			{
				$id_sett = $pag->getIdSettore();
				$id_soc = $pag->getIdSocieta();
				$id_tess = $pag->getIdTesserato();
				
				if($id_sett !== NULL)
					$pagati['settore'] += $pag->getQuota();
				
				if($id_tess !== NULL)
					$pagati['tesseramento'] += $pag->getQuota();
				
				$pagati['totale'] += $pag->getQuota();
			}
			
			$ar_non_pag = PagamentoUtil::get()->getNonPagati($id_soc, $anno);
			
			foreach ($ar_non_pag as $id_p=>$pag)
			{
				$id_sett = $pag->getIdSettore();
				$id_soc = $pag->getIdSocieta();
				$id_tess = $pag->getIdTesserato();
				
				if($id_sett !== NULL)
					$non_pag['settore'] += $pag->getQuota();
				
				if($id_tess !== NULL)
					$non_pag['tesseramento'] += $pag->getQuota();
				
				$non_pag['totale'] += $pag->getQuota();
			}
			
		}
		return array($pagati,$non_pag);
	}
}