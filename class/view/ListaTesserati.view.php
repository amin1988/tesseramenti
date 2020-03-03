<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('ListaTesserati');
include_view('QualificaView');

class ListaTesserati {
	
	private $ctrl;
	private $idtipo;
	private $func_urldett;
	private $func_urltess;
	
	/**
	 * @param int $id_soc
	 * @param int $idtipo
	 * @param callable $func_urldett funzione che prende un tesserato e 
	 * restituisce il link alla pagina dei dettagli
	 */
	public function __construct($id_soc, $idtipo, $func_urldett, $func_urltess)
	{
		$this->ctrl = new ListaTesseratiCtrl($id_soc, $idtipo);
		$this->idtipo = $idtipo;
		$this->func_urldett = $func_urldett;
		$this->func_urltess = $func_urltess;
	}
	
	public function stampaJsOnLoad() {
		echo <<<JS
var ricerca_dip = [];
 $('.dip-el').each(function () {
     var nome = $(this).text();
     var url = $(this).children("a").attr("href");
     ricerca_dip.push({
         value: nome,
         label: nome,
         url: url
     })
 });
	
 $("#ricerca_dip").autocomplete({
     source: ricerca_dip,
     select: function (event, ui) {
         location.href = ui.item.url;
         return false;
     }
 });
JS;
	}
	
	public function stampa()
	{
		
		//ricerca
		echo '<div class="ui-widget form-inline text-center" style="margin-bottom: 10px;"><label for="ricerca_dip">Ricerca:</label> ';
		echo '<input type="text" id="ricerca_dip" /></div>';
		
		echo '<table class="table table-striped">';
		echo "<thead><tr><th></th><th>Nome</th><th>Sesso</th><th>Data di nascita</th><th>Grado</th>";
		if (in_rinnovo()) {
			$anno = DataUtil::get()->oggi()->getAnno();
			echo "<th><div class=\"text-center\">Pagam. $anno</div></th>";
			$anno++;
			echo "<th><div class=\"text-center\">Pagam. $anno</div></th>";
		} else {
			echo "<th><div class=\"text-center\">Pagamento</div></th>";
		}
		echo "</tr></thead>";
		echo '<tbody>';
		foreach($this->ctrl->getTesserati() as $idtes=>$tes)
			$this->stampaRiga($tes);
		echo "</tbody></table>";
	}
	
	/**
	 * 
	 * @param Tesserato $tes
	 */
	public function stampaRiga($tes)
	{
		$url = call_user_func($this->func_urldett, $tes);
		$url_tess = call_user_func($this->func_urltess, $tes);
		if(!$tes->haCodiceFiscale())
		{
			$cl = "error";
			$inf = "<i class=\"icon-warning-sign\" title=\"CODICE FISCALE ASSENTE\"></i>";
		}
		else
		{
			$cl="";
			$inf = "";
		}
		echo "<tr class=\"$cl\" onclick=\"$url\" style=\"cursor:pointer\">";
		echo "<td><a href=\"$url\"><i class=\"icon-search\" title=\"Dettagli\"></i></a></td>";
		$n = $tes->getNome();
		$c = $tes->getCognome();
		echo "<td class=\"dip-el\">$inf <a href=\"$url_tess\" title=\"Apri scheda\"></a>$c $n</td>";
		$s = Sesso::toStringBreve($tes->getSesso());
		echo "<td>$s</td>";
		$dn = $tes->getDataNascita()->format('d/m/Y');
		echo "<td>$dn</td>";
		$q = $tes->getQualificaTipo($this->idtipo);
		$g = QualificaViewUtil::get()->getNome($q);
		echo "<td>$g</td>";
		$pl = PagamentoUtil::get()->getCorrentiTipo($tes->getId(), $this->idtipo);
		$anno = DataUtil::get()->oggi()->getAnno();
		$this->stampaColonnaPagamento($pl, $anno);
		if (in_rinnovo()) {
			//colonna anno prossimo
			$this->stampaColonnaPagamento($pl, $anno+1);
		}
		echo "</tr>";
	}
	
	/**
	 * @param Pagamento[] $pl
	 * @param int $anno
	 */
	private function stampaColonnaPagamento($pl, $anno) {
		echo '<td><div class="text-center">';
		if (isset($pl[$anno])) {
			$p = $pl[$anno];
			if($p->isPagato())
				echo "<i class=\"icon-ok\"></i>";
			else 
				echo "<i class=\"icon-remove\">";
		}
		echo '</div></td>';
	}
}