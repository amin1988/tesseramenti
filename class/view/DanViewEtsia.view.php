<?php
if (!defined('_BASE_DIR_')) exit();
include_model('KarateUtilETSIA');

class DanViewEtsia extends QualificaView {
	const CSS_CLASS_GRADO = 'grado_karatedan'; 
	const CSS_CLASS_EXTRA = 'datiextra_karatedan'; 
	
	/**
	 * Indica se il js per la sincronizzazione è già stato stampato
	 * @var bool
	 */
	private static $sincro = false;
	
	public static function getNome($qualifica) {
		if ($qualifica === NULL) return '';
		$grado = QualificaView::getNome($qualifica);
		$extra = $qualifica->getDatiExtra();
		if ($extra !== NULL)
			$grado .= ' ' . $extra->toString('dan'); 
		return $grado;
	}
	
	public function stampaJsOnload() {
		if ($this->form === NULL || self::$sincro) 
			return;
		self::$sincro = true;
		
		//sincronizza tutti i dan
		$css = self::CSS_CLASS_EXTRA;
		echo "$('.$css').change(function(){\n";
		echo "\tvar th = $(this);\n";
		echo "\t$('.{$css}_'+th.data('idt')).val(th.val());\n";
		echo "}).find(\"option[value='']\").text('Seleziona il kyu/dan');\n";
	}
	
	protected function stampaInner($idtesserato) {
		$fv = $this->form;
		$elkey = $this->getElemKey($idtesserato);
		if ($idtesserato === NULL) 
			$idt = 'NULL';
		else 
			$idt = $idtesserato;
		
		$css = self::CSS_CLASS_GRADO;
		$fv->stampa(FormElem_Grado::getNomeGrado($this->nomeEl), $elkey, array(
				'class' => "$css {$css}_{$this->idtipo}",
				'data-idt' => $idt,
				'data-tipo' => $this->idtipo
		));
		echo " ";
		$css = self::CSS_CLASS_EXTRA;
		$fv->stampa(FormElem_Grado::getNomeExtra($this->nomeEl, 'dan'), $elkey, array(
				'class' => "$css {$css}_$idt {$css}_{$this->idtipo}_{$idt}",
				'data-idt' => $idt
		));
	}
}
