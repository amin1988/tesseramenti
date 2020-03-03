<?php
if (!defined('_BASE_DIR_')) exit();

class WaitingView {
	private static $css = false;
	private static $js = false;
	
	private $nome;
	private $content;
	
	/**
	 * @param callable $contFunc funzione da chiamare per stampare il contenuto
	 * @param string $nome [opz] il nome da usare per gli id dei tag
	 */
	public function __construct($contFunc, $nome='waitingview') {
		$this->nome = $nome;
		$this->content = $contFunc;
	}
	
	public function stampaCss() {
		if (self::$css) return;
		self::$css = true;
		echo ".waitingview_big {\n";
		echo "	height: 60px;\n";
		echo "	background-image: url('"._PATH_ROOT_."img/wait.gif');\n}\n";
		echo ".waitingview_small {\n";
		echo "	height: 16px;\n";
		echo "	background-image: url('"._PATH_ROOT_."img/wait_small.gif');\n}\n";
		echo <<<CSS

.waitingview_wait {
	background-repeat:no-repeat;
	background-position:center; 
}

.waitingview_hidden {
	display: none;
}	
CSS;
	}
	
	public function stampaJs() {
		if (self::$js) return;
		self::$js = true;
		echo <<<JS

function waitingview_ready(nome) {
	if (nome == undefined) 
		nome = 'waitingview';
	$('#'+nome+'_waiting').addClass('waitingview_hidden');
	$('#'+nome+'_content').removeClass('waitingview_hidden');
}
						
JS;
	}
	
	public function stampa() {
		echo '<div class="waitingview_wait waitingview_big" id="'.$this->nome.'_waiting"></div>';
		echo '<div class="waitingview_content waitingview_hidden" id="'.$this->nome.'_content">';
		if (is_callable($this->content))
			call_user_func($this->content);
		echo '</div>';
	} 
}
