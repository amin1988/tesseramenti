<?php
if (!defined("_BASE_DIR_")) exit();
include_controller('NuovoTesserato');
include_class('Sesso');
include_view('FormViewTesserato');

class NuovoTesserato extends FormViewTesserato {
	/**
	 * @param int $idsocieta ID società in cui inserire il nuovo tesserato
	 * @param callable $callback funzione da chiamare in caso di esito positivo
	 * del tipo <code>funzione(Tesserato)</code>
	 */
	function __construct($idsocieta, $callback, $pulsanti=true) {
		parent::__construct(new NuovoTesseratoCtrl($idsocieta, $callback), $pulsanti);
	} 
}
