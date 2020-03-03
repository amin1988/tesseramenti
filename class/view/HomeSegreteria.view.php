<?php
if (!defined('_BASE_DIR_')) exit();
include_controller('HomeSegreteria');
include_model('Federazione');
include_formview('FormView');

class HomeSegreteria extends ViewWithForm {
	private $ctrl;
	private $tess;
	private $soc;
	
	public function __construct()
	{
		$this->ctrl = new HomeSegreteriaCtrl();
		
		$this->tess = $this->ctrl->getTesserati();
		$this->soc = $this->ctrl->getSocieta();
	}
	
	public function stampa()
	{
		
		?>
	<div class="container-fluid">
  		<div class="row-fluid">
    		<div class="span6">
    		<h4>Ultimi tesserati</h4>
	    		<div class="well">
	      			<table class="table table-hover table-condensed table-bordered">
		      			<thead><tr><td><strong>Nome</strong></td><td><strong>Cognome</strong></td><td><strong>Sesso</strong></td><td><strong>Societ&agrave</strong></td></tr></thead>
		      			<tbody>
		      			<?php
			      			foreach($this->tess as $idt=>$t)
			      			{
			      				/* @var $t Tesserato */
			      				$n = $t->getNome();
			      				$c = $t->getCognome();
			      				$s = Sesso::toStringBreve($t->getSesso());
			      				$soc = Societa::fromId($t->getIDSocieta())->getNomeBreve();
			      					
			      				echo "<tr><td>$n</td><td>$c</td><td>$s</td><td>$soc</td></tr>";
			      			}
		      			?>
		      			</tbody>
	      			</table>
	    		</div>
    		</div>
    		<div class="span6">
    		<h4>Ultime societ&agrave</h4>
      			<div class="well">
      				<table class="table table-hover table-condensed table-bordered">
		      			<thead><tr><td><strong>Nome</strong></td><td><strong>Federazione</strong></td></tr></thead>
		      			<tbody>
		      			<?php
		      				foreach($this->soc as $ids=>$s)
		      				{
		      					/* @var $s Societa */
		      					$n = $s->getNome();
		      					$f = Federazione::fromId($s->getIdFederazione())->getNome();
		      					
		      					echo "<tr><td>$n</td><td>$f</td></tr>";
		      				}
		      			?>
		      			</tbody>
		      		</table>
      			</div>
    		</div>
  		</div>
	</div>
		<?php
	}
}