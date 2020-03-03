<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('DettagliTesseratiSocieta');



class DettagliTesseratiSocieta {
	
	private $ctrl;
	private $admin;
	
	/**
	 * @param int $id_soc
	 * @param bool $admin [def:false] true per la visualizzazione amministratore
	 */
	function __construct($id_soc, $admin=false) {
		$this->ctrl = new DettagliTesseratiSocietaCtrl($id_soc);
		$this->admin = $admin;
	} 
	
	function stampa()
	{
// 		$soc = $this->ctrl->getSocieta();
		?> 
  <div class="row-fluid">
    <div class="span4 tab-label">Attivi:</div>
    <div class="span8"><?php echo count($this->ctrl->getAttivi()); ?></div>
  </div>
  <div class="row-fluid">
    <div class="span4 tab-label">Non Attivi:</div>
    <div class="span8"><?php echo count($this->ctrl->getNonAttivi()); ?></div>
  </div>
  <?php if(in_rinnovo()) {?>
  <div class="row-fluid">
    <div class="span4 tab-label">Rinnovati:</div>
    <div class="span8"><?php echo count($this->ctrl->getRinnovati()); ?></div>
  </div>
		<?php 
		}
	} //function stampa()

}
