<?php
if (!defined('_BASE_DIR_')) exit();

class FormView_data extends FormView_Elem {
	/**
	 * chiave dell'attributo da usare per impostare la stringa di suggerimento
	 */
	const ATTR_HINT = 'consiglio';
	
	function stampaJsOnload() {
		$nome = $this->elem->getNomeKey();
		echo "var dp = \$(\"#form_$nome\");\n";
		echo '$.datepicker.setDefaults($.datepicker.regional[ "it" ]); ';
		echo 'dp.datepicker({';
		echo ' changeMonth: true, changeYear: true ';
		echo "});\n";
		foreach (array('min'=>$this->elem->getMin(), 'max'=>$this->elem->getMax()) as $b=>$d) {
			if ($d !== NULL) {
				$a = $d->getAnno();
				$m = $d->getMese()-1;
				$g = $d->getGiorno();
				echo "dp.datepicker( \"option\", \"{$b}Date\", new Date($a, $m, $g) );\n";
			}
		}
	}
	
	/**
	 * @param array $attr valori:
	 *                          FormView_data::ATTR_HINT     string|NULL
	 *                                Valore da mostrare a fianco del campo
	 * @see FormView_Elem::stampa()
	 */
	function stampa($attr) {
		$nome = $this->elem->getNomeKey();
		$val = $this->elem->getDefault();
		/* @var $val Data */
		if ($val === NULL)
			$val='';
		else
			$val = $val->format('d/m/Y');
		echo '<input type="text" ';
		
		if ($attr !== NULL && (isset($attr[self::ATTR_HINT]) 
				|| array_key_exists(self::ATTR_HINT, $attr))) 
		{
			$hint = $attr[self::ATTR_HINT];
			unset($attr[self::ATTR_HINT]);
		} else {
			$hint = 'gg/mm/aaaa';
		}
		
		if (!isset($attr['pattern']))
			$attr['pattern'] = "[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}";
		if (!isset($attr['title']))
			$attr['title'] = "Formato: $hint";
		if (!isset($attr['placeholder']) && $hint !== NULL)
			$attr['placeholder'] = "$hint";
		
		if ($nome !== NULL)
			echo "name=\"$nome\" id=\"form_$nome\" ";
		if ($this->elem->isDisabilitato() === true)
			echo 'disabled="disabled" ';
		if ($this->elem->isObbligatorio())
			echo 'required="required" ';
		FormView::stampaAttr($attr);
		echo "value=\"$val\" />";
		if ($hint !== NULL) echo " ($hint)";
		echo "\n";
	}
}