<?php
if (!defined('_BASE_DIR_')) exit();
include_model('Societa','Comune','Provincia','Regione');

class ListaSocieta {
	private $callback;
	private $federazione;
	private $stat;
	private $settore = NULL;
	private static function ordinaSoc($sa, $sb) {
		return strcmp($sa->getNome(), $sb->getNome());
	}
	
	/**
	 * @param callable $callback funzione che prende un oggetto
	 * Societa e restituisce l'url da associare al click 
	 */
	public function __construct($callback,$federazione=NULL,$stat=false) {
		$this->callback = $callback;
		$this->federazione = $federazione;
		$this->stat = $stat;
	}
	
	public function setSettore($sett)
	{
	        $this->settore = $sett;
	}
	
	public function stampaJsOnload() {
		echo <<<JS
var ricerca_soc = [];
 $('.soc-el').each(function () {
     var nome = $(this).text();
	        
     var url = $(this).children("a").attr("href");
	        
     ricerca_soc.push({
         value: nome,
         label: nome,
         url: url
     })
 });

 $("#ricerca_soc").autocomplete({
     source: ricerca_soc,
     select: function (event, ui) {
         location.href = ui.item.url;
         return false;
     }
 });		
JS;
		
		

		
		
	}
	
	public function stampa() {
	        if ( !is_null($this->settore))
	        {
	                $regione = isset($_POST['ricerca_reg']) ? $_POST['ricerca_reg'] : NULL;
	                $soc = Societa::listaCompletaKudo($regione);
	        }
	        else{
		$soc = Societa::listaCompleta($this->federazione);
	        }
		uasort($soc, array(__CLASS__,'ordinaSoc'));
		
// 		//ricerca
// 		echo 'Cerca: <input type="text" data-provide="typeahead" data-source=\'[';
// 		$primo = true;
// 		foreach ($soc as $s) {
// 			if ($primo) $primo = false;
// 			else echo ',';
// 			echo '"'.str_replace('"', '', $s->getNome()).'"';
// 		}
// 		echo "]'>\n";

		//ricerca
		echo '<div class="ui-widget form-inline text-center"><label for="ricerca_soc">Ricerca:</label> ';
		echo '<input type="text" id="ricerca_soc" /></div>';
		print '<p></p>';
		
		/*
		echo '<div class="ui-widget form-inline text-center"><label for="ricerca_reg">Ricerca:</label> ';
		echo '<input type="text" id="ricerca_reg" /></div>';
		*/
		print '<form action="" method="Post" > ';
		
		echo '<div class="ui-widget form-inline text-center"><label for="ricerca_reg">Ricerca per regione:</label> ';
		echo '<input type="text" name="ricerca_reg" id="ricerca_reg"  />';
		echo ' <input type="submit" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">  '
		. ' </div>';
		//elenco
		if($this->stat)
		{
// 			echo "<br>";
			echo "<table class=\"table table-condensed table-bordered\" style=\"margin-top: 25px;\">";
			echo "<thead><tr><td>Tesserati</td><td>Settori</td><td>Societ&agrave</td><td>Regione</td></tr></thead>";
		}
		else
			echo '<ul>';
			
		foreach ($soc as $s) {
			/* @var $s Societa */
			$id = $s->getId();
			$n = $s->getNome();
			$url = call_user_func($this->callback, $s);
			
			if($this->stat)
			{
				$num_t = Pagamento::numTessCorrenti($id);
				$num_s = Pagamento::numSettCorrenti($id);
				$sep = "";
				
				if($num_t < 100) $sep .= " ";
				if($num_t < 10) $sep .= " "; 
				
// 				echo "<li class=\"soc-el\"><a href=\"$url\">$n</a>";//</li>\n";
// 				echo "<ul><li><i class=\"icon-user\"></i>$num_t</li><li><i class=\"icon-briefcase\"></i>$num_s</li></ul></li>\n";

				if(($num_s + $num_t) == 0)
					$class = "error";
				else 
					$class = "success";
				
				$idc = $s->getIdComune();
				$comune = Comune::fromId($idc);
			                   $prov = Provincia::fromId($comune->getIDProvincia());
			                   $regione_nome  = Regione::fromId($prov->getIDRegione())->getNome();
				echo "<tr class=\"$class\"><td><i class=\"icon-user\" title=\"Tesserati\"></i> $num_t</td><td><i class=\"icon-briefcase\" title=\"Settori\"></i> $num_s</td><td class=\"soc-el\"><a href=\"$url\"> $n</a></td>        <td class=\"soc-reg\"><a href=\"$url\"> $regione_nome</a></td>     </tr>";
			}
			else
			{
				echo "<li class=\"soc-el\"><a href=\"$url\">$n</a></li>\n";
			}
		}
		print '</form> ';
		if($this->stat)
			echo "</table>";
		else
			echo "</ul>";
	}
	
	public function stampaReg()
	{
		foreach(Regione::listaCompleta() as $idr=>$reg)
		{
			
		}
	}
}