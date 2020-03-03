<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('SocietaIdonee');
include_model('Societa');
include_formview('FormView');

class SocietaIdonee {
	
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new SocietaIdoneeCtrl();
	}
	
	public function stampa()
	{
		$anno = DataUtil::get()->oggi()->getAnno();
		$anno_1 = $anno - 1;
		echo "<h3>Societ&agrave idonee per assemblea</h3>";
		echo "<h4>Pagamenti registrati per gli anni $anno_1 e $anno</h4>";
		echo "<a class=\"btn btn-info nostampa\" href=\"javascript:window.print()\" style=\"margin-bottom: 10px;\">Stampa <i class=\"icon-print icon-white\"></i></a>";
		$idonee = $this->ctrl->getIdonee();
		$consigli = $this->ctrl->getConsiglio();
		
		?>
		<center>
		<table class="table table-condensed table-striped table-hover table-bordered">
		<thead><tr><th><center>Societ&agrave</center></th><th><center>Presidente</center></th><th><center>Vicepresidente</center></th><th><center>Segretario</center></th><th><center>Consigliere</center></th><th><center>Consigliere</center></th><th><center>Consigliere</center></th><th><center>Consigliere</center></th><th><center style="width: 200px;">Nome e Firma</center></th></tr></thead>
		<tbody>
		<?php
			foreach($idonee as $idsocieta=>$societa)
			{
				$nome = ucwords(strtolower($societa->getNome()));
				echo "<tr><td><center>$nome</center></td>";
				foreach($consigli[$idsocieta] as $cons)
				{
					echo "<td><center>$cons</center></td>";
				}
				echo "<td></td></tr>";
			}
		?>
		</tbody>
		</table>
		</center>
		<?php
	}
}