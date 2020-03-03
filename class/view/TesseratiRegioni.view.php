<?php
if (!defined('_BASE_DIR_')) exit();
include_controller('TesseratiRegioni');
include_model('Regione','Provincia','Comune','Settore');
include_view('QualificaView');

class TesseratiRegioni {
	
	private $ctrl;
	private $anno;
	
	function __construct($anno) {
		$this->ctrl = new TesseratiRegioniCtrl($anno);
		$this->anno = $anno;
	} 
	
	function stampa()
	{
		$tot = $this->ctrl->getTotaliRegioni();
		$totp = $this->ctrl->getTotaliProvince();
		$reg = $this->ctrl->getRegioni();
		$prov = $this->ctrl->getProvince();
		
		echo "<center><h3>Anno $this->anno</h3></center>";
		
		echo "<table>";
		
		foreach($reg as $idr=>$r)
		{
			if($idr == 1)
				echo "<tr>";
			elseif ($idr %3 == 1 && $idr >2)
				echo "</tr><tr>";
			
			$rn = $r->getNome();
			$tr = $tot[$idr];
			
			echo "<td  style=\"padding-right:25px\">";
			echo "<h3>$rn</h3>";
			echo "<div class=\"well\" style=\"width: 300px;\">";
			echo "<h4>Totale regione: $tr tesserati</h4>";
			if($tr > 0)
			{
				foreach(Provincia::listaRegione($r) as $idp=>$p)
				{
					$pn = $p->getNome();
					$tp = $totp[$idp];
					
					if($tp > 0)
						echo "<h6>$pn - $tp tesserati</h6>";
				}
			}
			echo "</div>";
			echo "</td>";
			
			if($idr == 20)
				echo "</tr></table>";
		}
		
	} //function stampa()
	
	public function stampaJsOnload() {
	}
}
