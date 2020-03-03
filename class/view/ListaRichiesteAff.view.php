<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('ListaRichiesteAff');
include_model('Comune','Provincia','Regione','Settore');

class ListaRichiesteAff {
	
	private $ctrl;
	private $func_urldett;
	
	public function __construct($func_urldett)
	{
		$this->ctrl = new ListaRichiesteAffCtrl();
		$this->func_urldett = $func_urldett;
	}
	
	public function stampa()
	{
		if(count($this->ctrl->getListaRich()) == 0)
			echo "<h4>Nessuna richiesta presente</h4>";
		else 
		{
			echo '<table class="table table-striped">';
			echo "<thead><tr><th>Nome</th><th>Settori</th><th>Regione</th><th>Provincia</th><th>Comune</th><th></th></tr></thead>";
			echo '<tbody>';
			
			foreach($this->ctrl->getListaRich() as $idrich=>$rich)
			{
				$this->stampaRiga($rich);
			}
			echo '</tbody></table>';
		}
	}
	
	/**
	 * 
	 * @param RichiestaAff $rich
	 */
	public function stampaRiga($rich)
	{
		$primo = true;
		$nsoc = $rich->getNome();
		echo "<tr><td>$nsoc</td>";
		echo "<td> ";
		foreach($rich->getSettori() as $idsett)
		{
			$nsett = Settore::fromId($idsett)->getNome();
			if($primo)
			{
				echo "$nsett";
				$primo = false;
			}
			else 
				echo ", $nsett";
			
			
		}
		echo "</td>";
		
		$com = Comune::fromId($rich->getIDComune());
		$prov = Provincia::fromId($com->getIDProvincia());
		$reg = Regione::fromId($prov->getIDRegione());
		$nreg = $reg->getNome();
		$nprov = $prov->getNome();
		$ncom = $com->getNome();

		echo "<td>$nreg</td>";
		echo "<td>$nprov</td>";
		echo "<td>$ncom</td>";
		$url = call_user_func($this->func_urldett, $rich);
		echo "<td><a href=\"$url\"><i class=\"icon-play\"></i></a></td></tr>";
	}
	
}