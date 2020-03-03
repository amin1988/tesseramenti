<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Utente','Societa');
include_controller('GestioneUtenti');

class ListaUtenti {
	private $ctrl;
	private $callback;
	
	/**
	 * 
	 * @param Utente $ua
	 * @param Utente $ub
	 * @return number
	 */
	private static function ordinaUt($ua, $ub) {
		if($ua->getSocieta() !== NULL)
			$str_a = $ua->getSocieta()->getNome();
		else 
			$str_a = "Admin";
		
		if($ub->getSocieta() !== NULL)
			$str_b = $ub->getSocieta()->getNome();
		else
			$str_b = "Admin";
		
		return strcmp($str_a, $str_b);
	}
	
	/**
	 * @param callable $callback funzione che prende un oggetto
	 * Societa e restituisce l'url da associare al click 
	 */
	public function __construct($callback) {
		$this->ctrl = new GestioneUtentiCtrl(NULL);
		$this->callback = $callback;
	}
	
	public function stampaJsOnload()
	{
		echo <<<JS
		var ricerca_ute = [];
 $('.ute-el').each(function () {
     var nome = $(this).text();
     var url = $(this).children("a").attr("href");
     ricerca_ute.push({
         value: nome,
         label: nome,
         url: url
     })
 });

 $("#ricerca_ute").autocomplete({
     source: ricerca_ute,
     select: function (event, ui) {
         location.href = ui.item.url;
         return false;
     }
 });		
JS;
	}
	
	public function stampa() {
		$ute = $this->ctrl->getUtenti();
		uasort($ute, array(__CLASS__,'ordinaUt'));
		
		//ricerca
		echo '<div class="ui-widget form-inline text-center"><label for="ricerca_soc">Ricerca:</label> ';
		echo '<input type="text" id="ricerca_ute" /></div>';
		
		//nuovo utente
		echo "<a href=\"utente.php\" class=\"btn btn-primary\">Nuovo Utente</a>";
		echo "<br><br>";
	
		//elenco
		echo '<ul>';
		foreach ($ute as $u) {
			/* @var $u Utente */
			$s = $u->getSocieta();
			if($s !== NULL)
				$n = $s->getNome().' - '.$u->getUsername();
			else 
				$n = "Admin - ".$u->getUsername();
			$url = call_user_func($this->callback, $u);
			echo "<li class=\"ute-el\"><a href=\"$url\">$n</a></li>\n";
		}
		echo '</ul>';
	}
	
}