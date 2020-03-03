<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('SpostaTesserati');
include_model('Societa');
include_formview('FormView');

class SpostaTesserati extends ViewWithForm {
	
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new SpostaTesseratiCtrl();
		$this->form = new FormView($this->ctrl->getForm());
	}
	
	public function stampa()
	{
		$fv = $this->form;
		$fv->stampaInizioForm();
		
		echo '<div class="form-horizontal">';
		if($_SESSION['soc_s'] === NULL || $_SESSION['soc_d'] === NULL)
		{
			?>
		<div class="control-group">
	   		<label class="control-label" for="form_<?php echo SpostaTesseratiCtrl::DA_SOC; ?>">Societ&agrave partenza:</label>
			<div class="controls"><?php $fv->stampa(SpostaTesseratiCtrl::DA_SOC); ?> </div>
		</div>
		 
		<div class="control-group">
			<label class="control-label" for="form_<?php echo SpostaTesseratiCtrl::A_SOC; ?>">Societ&agrave destinazione:</label>
			<div class="controls"><?php $fv->stampa(SpostaTesseratiCtrl::A_SOC); ?></div>
		</div>
		
		<div class="control-group">
		<div class="controls"><?php $fv->stampa(SpostaTesseratiCtrl::SEL_SOC, NULL, array("class"=>"btn btn-primary")); ?></div>
		</div>
			<?php
		}
		else 
		{
			$sorg = $this->ctrl->getSocieta($_SESSION['soc_s']);
			$dest = $this->ctrl->getSocieta($_SESSION['soc_d']);
			?>
		<div class="well well-small">
			<?php echo "<p class=\"text-center\">Sposta tesserati da <strong>$sorg</strong> a <strong>$dest</strong></p><br>
			<p class=\"text-center\">N.B.: l'ultimo pagamento valido del tesserato passer&agrave dalla societ&agrave $sorg alla societ&agrave $dest</p>"; ?>
		</div>
			<?php
			$tess = $this->ctrl->getTesserati();
			
			if(count($tess) > 0)
			{
				echo "<div class=\"well\">";
				echo "<table class=\"table table-bordered table-condensed\">";
				$i=0;
				foreach($tess as $idtes=>$tess)
				{
					if($i %4 == 0 )
						echo "<tr>";
					
					/* @var $tess Tesserato */
					$nc = ucwords(strtolower($tess->getNome().' '.$tess->getCognome()));
				?>
		<td>
			<div class="control-group">
				<div class="controls" style="margin-left: 10px;"><label class="checkbox"><?php $fv->stampa(SpostaTesseratiCtrl::TESS,$idtes); echo ' '.$nc;?></label></div>
			</div>
		</td>
				<?php
					$i++;
					if($i %4 == 0 )
						echo "</tr>";
				}
				
				if($i %4 != 0)
				{
					while($i %4 != 0)
					{
						echo "<td>&nbsp;</td>";
						$i++;
					}
					echo "</tr>";
				}
				echo "</table></div>";
			}
			?>
		<div class="control-group">
			<div class="controls"><?php $fv->stampa(SpostaTesseratiCtrl::SEL_TESS, NULL, array("class"=>"btn btn-primary")); ?></div>
		</div>
			<?php
		}
		echo '</div>';
		$fv->stampaFineForm();
	}
}