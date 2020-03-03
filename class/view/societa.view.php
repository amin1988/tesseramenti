<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('DettagliSocieta');



class DettagliSocieta {
	
	private $ctrl;
	private $admin;
	
	/**
	 * @param int $id_soc
	 * @param bool $admin [def:false] true per la visualizzazione amministratore
	 */
	function __construct($id_soc, $admin=false) {
		$this->ctrl = new DettagliSocietaCtrl($id_soc);
		$this->admin = $admin;
	} 
	
	function data($data) {
		if ($data !== NULL)
			echo $data->format('d/m/Y');
	}

	/**
	 * Restituisce i settori della societa separati da una virgola
	 * @return string
	 */
	private function compattaSettori() {
		$set = array();
		foreach ($this->ctrl->getSettori() as $settore)
		{
			/* @var $settore Settore */
			$set[$settore->getId()] = $settore->getNome();
		}
			
		asort($set);
		echo '<ul class="unstyled">';
		foreach ($set as $id=>$nome) {
			echo '<li>';
			if (!$this->ctrl->settoreRinnovato($id))
				echo '<i class="icon-exclamation-sign" title="Settore non rinnovato"></i> ';
			echo "$nome</li>\n";
		}
		echo "</ul>\n";
	
	}
	
	function stampa()
	{
		$soc = $this->ctrl->getSocieta();
		?> 
  <div class="row-fluid">
    <div class="span4 tab-label">Codice:</div>
    <div class="span8"><?php echo $soc->getCodice(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Codice Acsi:</div>
    <div class="span8">
    <?php
    	echo $soc->getCodiceAcsi(); 
    	if ($this->admin && $soc->isFileAcsiEsistente()) {
    		$url = _PATH_ROOT_ . $soc->getFileAcsi();
			echo " <a href=\"$url\"><i class=\"icon-file\"></i></a>";
		}
    ?>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Federazione:</div>
    <div class="span8"><?php echo $this->ctrl->getFederazione(); ?></div>
  </div>
    <div class="row-fluid">
    <div class="span4 tab-label">Nome:</div>
    <div class="span8"><?php echo $soc->getNome(); ?></div>
  </div>
    <div class="row-fluid">
    <div class="span4 tab-label">Nome breve:</div>
    <div class="span8"><?php echo $soc->getNomeBreve(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Data costituzione:</div>
    <div class="span8"><?php $this->data($soc->getDataCostituzione()); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Partita IVA:</div>
    <div class="span8"><?php echo $soc->getPIva(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Regione:</div>
    <div class="span8"><?php echo $this->ctrl->getRegione(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Comune:</div>
    <div class="span8"><?php echo $this->ctrl->getComune(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Sede Legale:</div>
    <div class="span8"><?php echo $soc->getSedeLegale(); ?></div>
  </div>
    <div class="row-fluid">
    <div class="span4 tab-label">CAP:</div>
    <div class="span8"><?php echo $soc->getCAP(); ?></div>
  </div>
    <div class="row-fluid">
    <div class="span4 tab-label">Telefono:</div>
    <div class="span8"><?php echo $soc->getTel(); ?></div>
  </div>
    <div class="row-fluid">
    <div class="span4 tab-label">Fax:</div>
    <div class="span8"><?php echo $soc->getFax(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Email:</div>
    <div class="span8"><?php echo $soc->getEmail(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Sito web:</div>
    <div class="span8"><?php echo $soc->getSito(); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Data Inserimento:</div>
    <div class="span8"><?php $this->data($soc->getDataInserimento()); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Settori:</div>
    <div class="span8"><?php echo $this->compattaSettori(); ?></div>
  </div>
		<?php 
	} //function stampa()

}
