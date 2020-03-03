<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('GeneraTesserini');
include_model('Societa');
include_formview('FormView');

class GeneraTesserini extends ViewWithForm {
	
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new GeneraTesseriniCtrl();
		$this->form = new FormView($this->ctrl->getForm());
	}
	
	public function stampa()
	{
		$fv = $this->form;
		$fv->stampaInizioForm();
		
		echo "<div class=\"well\"><center><b>Verranno generati i tesserini solo per i tesserati in regola con il pagamento ed assicurati<br>";
		echo "Verranno inoltre generati i tesserini per i membri del consiglio</b></center></div>";
		
		echo "Seleziona le societ&agrave per le quali generare i tesserini";
		
		echo "<div class=\"well\">";
		echo "<center><table><tr>";
		$i = 0;
		foreach ($this->ctrl->getSocieta() as $id_s=>$soc)
		{
			if($i % 5 === 0 && $i != 0)
				echo "</tr><tr>";
			?>
				<td>
					<div class="control-group">
						<div class="controls"><label class="checkbox"><?php $fv->stampa(GeneraTesseriniCtrl::SOC_CH, $id_s); echo $soc->getNomeBreve()?></label></div>
					</div>
				</td>
			<?php
			$i++;
		}
		echo "</tr></table></center></div>";
		
		?>
		<div class="control-group">
		   		<div class="controls"><?php $fv->stampa(GeneraTesseriniCtrl::GENERA, NULL, array("class"=>"btn btn-primary")); ?></div>
		</div>
		<?php
	}
	
	
	
}