<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Utente','Societa');
include_controller('GestioneUtenti');
include_formview('FormView');

class GestioneUtente extends ViewWithForm {
	private $ctrl;
	private $callback;
	private $id_utente;
	
	public function __construct($id_utente)
	{
		$this->ctrl = new GestioneUtentiCtrl($id_utente);
		$this->id_utente = $id_utente;
		$this->form = new FormView($this->ctrl->getForm());
	}
	
	private function stampaRiga($label, $fv, $nome, $id=NULL, $attr=NULL) {
		$err = $this->ctrl->getErrore($nome);
		$el = $fv->getElem($nome);
		$obbl = $el->getFormElem()->isObbligatorio();
		if($id !== NULL)
			$str_id = "id=\"$id\"";
		else 
			$str_id = '';
		echo '<div '.$str_id.' class="control-group';
		if ($err != '') echo ' error';
		echo '">';
		echo "\n<label class=\"control-label";
		if ($obbl) echo ' obbligatorio';
		echo "\" for=\"form_$nome\">";
		if ($obbl) echo '* ';
		echo "$label:</label>\n";
		echo '<div class="controls">';
		$el->stampa($attr);
			echo ' <span class="help-inline">';
		echo $err;
		echo '</span>';
		echo "</div></div>\n";
	}
	
	public function stampa()
	{
		$fv = $this->form;
		$fv->stampaInizioForm();
		
		echo '<div class="form-horizontal">';
		echo '<div class="control-group"><div class="controls obbligatorio">* Campi obbligatori</div></div>';
		
		$fv = $this->form;
		$fv->stampaInizioForm();
		
		echo '<div class="control-group"><div class="controls sezione"><h4>Dati personali</h4></div></div>';
		$this->stampaRiga('Username', $fv, GestioneUtentiCtrl::USERNAME);
		$this->stampaRiga('Password', $fv, GestioneUtentiCtrl::PASSWORD);
		if($this->id_utente == 0)
			$this->stampaRiga('Ripeti Password', $fv, GestioneUtentiCtrl::PASSWORD_2);
		$this->stampaRiga('Email', $fv, GestioneUtentiCtrl::EMAIL);
		$this->stampaRiga('Tipo', $fv, GestioneUtentiCtrl::TIPO);
		$this->stampaRiga('Societ&agrave', $fv, GestioneUtentiCtrl::SOCIETA, "societa");
		
		?>
	<div class="control-group">
		<div class="controls"><?php $fv->stampa(GestioneUtentiCtrl::SALVA, NULL, array('class'=>'btn-primary')); ?>
		<?php 
		if($this->id_utente != 0)
			echo "<a href=\"elimina.php?id_utente=$this->id_utente\" class=\"btn btn-danger\">Elimina</a>"; 
		?>
		<a href="utenti.php" class="btn">Annulla</a></div>
	</div>
		
		<?php
		
		$fv->stampaFineForm();
		
	}
	
	public function stampaJsOnload()
	{
		echo <<<soc
		var tipo = $("#form_tipo").val();
		if(tipo == 2)
			$("#societa").show();
		else
			$("#societa").hide();
		\n
soc;
		
		echo "$(\"#form_".GestioneUtentiCtrl::TIPO."\").change(function(){\n";
		echo <<<js
		var sel_a = $(this).val();
		if (sel_a == 2)
			$("#societa").show();
		else
			$("#societa").hide();
		});
js;
	}
}