<?php
if (!defined('_BASE_DIR_')) exit();

include_view('DanView');

class DanAtletaView extends DanView {
	private static $jsOnLoad = false;
	
	public static function getNome($qualifica) {
		if ($qualifica === NULL) return '';
		
		//se è nera
		if ($qualifica->getIdGrado() == KarateUtil::get()->getIdNera()) {
			//cintura e dan
			return DanView::getNome($qualifica);
		} else {
			//solo cintura
			return QualificaView::getNome($qualifica);
		}
	}
	
	public function stampaJsOnload() {
		if ($this->form === NULL) return;
		
		if (!self::$jsOnLoad) {
			//valida per tutti gli atleti, stampa solo una volta
			self::$jsOnLoad = true;
			$grado = '".'.DanView::CSS_CLASS_GRADO.'_1"';
			$extra = '".'.DanView::CSS_CLASS_EXTRA.'_"+$(this).data("tipo")+"_"+$(this).data("idt")';
			
			//nasconde dan quando non si seleziona la nera
			$idnera = KarateUtil::get()->getIdNera();
			echo "$($grado).change(function(){\n\t$($extra).toggle(this.value == $idnera);\n}).change();\n";
		}
		
		parent::stampaJsOnload();
	} 
}

QualificaViewUtil::get()->_addClass(1, 'DanAtletaView');
