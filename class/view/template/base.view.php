<?php
if (!defined("_BASE_DIR_")) exit();

/**
 * Attributa di TemplateBase per impostare la sezione attuale
 */
define('TMPL_ATTR_SEZ','sezione');

abstract class TemplateBase {
	private $titolo=NULL;
	/**
	 * array di stringhe o View
	 * @var array
	 */
	private $body;
	/**
	 * @var View[]
	 */
	private $views = NULL;
	private $code = NULL;
	protected $attr = array();
	
	public function setAttr($nome, $val) {
		$this->attr[$nome] = $val;
	}
	
	public function getAttr($nome) {
		if (isset($this->attr[$nome]))
			return $this->attr[$nome];
		else
			return NULL; 
	}
	
	public function setTitolo($titolo) {
		$this->titolo = $titolo;
	}

	protected function menuClass($menu, $base='') {
		echo "class=\"$base ";
		if ($menu == $this->getAttr(TMPL_ATTR_SEZ))
			echo 'active';
			echo '"';
	}
	
	private function addInclude($list, $type) {
		foreach ($list as $f) {
			$this->code[$type][$f] = $f;
		}
	}
	
	private function addCode($code, $type) {
		if (isset($this->code[$type]))
			$this->code[$type] .= "\n\n".$code;
		else 
			$this->code[$type] = $code;
	}
	
	public function addCssInclude($file) {
		$this->addInclude(func_get_args(), 'css-inc');
	}
	
	public function addCssCode($code) {
		$this->addCode($code, 'css-code');
	}
	
	public function addJsInclude($file) {
		$this->addInclude(func_get_args(), 'js-inc');
	}
	
	public function addJsOnload($code) {
		$this->addCode($code, 'js-load');
	}
	
	public function addJsCode($code) {
		$this->addCode($code, 'js-code');
	}
	
	public function addBody($elem) {
		$n = func_num_args();
		for($i=0; $i<$n; $i++) 
			$this->body[] = func_get_arg($i);
	}
	
	private function mergeInclude($type, $func) {
		if (isset($this->code[$type]))
			$m = $this->code[$type];
		else
			$m = array();
		foreach ($this->getListaElementi() as $parte) {
			if (method_exists($parte, $func)) {
				$pi = $parte->$func();
				if ($pi !== NULL) {
					foreach ($pi as $f)
						$m[$f] = $f;
				}
			}
		}
		return $m;
	}
	
	private function stampaCode($open, $close, $type, $func) {
		$open = "$open\n";
		$primo = true;
		foreach ($this->getListaElementi() as $parte) {
			if (method_exists($parte, $func)) {
				if ($primo) {
					echo $open;
					$primo = false;
				}
				$parte->$func();
			}
		}
		if (isset($this->code[$type])) {
			if ($primo) {
				echo $open;
				$primo = false;
			}
			echo $this->code[$type];
		}
		if (!$primo) echo "\n$close";
	}
	
	private function getListaElementi() {
		if ($this->views === NULL) {
			if ($this->body === NULL) 
				$this->views = array();
			else {
				$it = new ArrayIterator($this->body);
				foreach ($it as $parte) {
					if (method_exists($parte, 'getSubview')) {
						$sv = $parte->getSubview();
						if ($sv !== NULL) {
							if (is_array($sv)) {
								foreach ($sv as $v)
									$it->append($v);
							} else 
								$it->append($sv);
						}
					}
				}
				$this->views = $it->getArrayCopy();
			}
		}
		return $this->views;
	}

	private function stampaCssInclude() {
		foreach ($this->mergeInclude('css-inc', 'getCssInclude') as $f) {
			echo "<link href=\""._PATH_ROOT_."css/$f.css\" rel=\"stylesheet\">\n";
		}
	}

	private function stampaJsInclude() {
		foreach ($this->mergeInclude('js-inc', 'getJsInclude') as $f) {
			echo "<script src=\""._PATH_ROOT_."js/$f.js?nocache\"></script>\n";
		}
	}
	
	private function stampaBody() {
		if ($this->body === NULL) return;
		foreach ($this->body as $parte) {
			if (is_string($parte)) {
				echo $parte;
			} else {
				$parte->stampa();
			}
		}
	}
	
	protected abstract function stampaMenu();
	protected function stampaSubMenu() {}
	protected function subdirGare() {
		return '';
	}
	
	public function stampa() {
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php if ($this->titolo !== NULL) echo $this->titolo.' - '; ?>Tesseramento FIAM</title>

<link href="<?php echo _BOOTSTRAP_URL_; ?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo _BOOTSTRAP_URL_; ?>css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="<?php echo _JQUERYUI_THEME_; ?>" rel="stylesheet">
<link href="<?php echo _PATH_ROOT_; ?>css/base.css" rel="stylesheet">
<link href="<?php echo _PATH_ROOT_; ?>css/print.css" rel="stylesheet" type="text/css" media="print">
<?php 
$this->stampaCssInclude(); 
$this->stampaCode('<style>', '</style>', 'css-code', 'stampaCss');
?>


<script src="<?php echo _JQUERY_URL_; ?>"></script>
<script src="<?php echo _JQUERYUI_JS_; ?>"></script>
<script src="<?php echo _JQUERYUI_URL_; ?>/i18n/jquery.ui.datepicker-it.min.js"></script>
<script src="<?php echo _BOOTSTRAP_URL_; ?>js/bootstrap.min.js"></script>
<script src="<?php echo _PATH_ROOT_; ?>js/common.js"></script>
<?php $this->stampaJsInclude() ?>

<script type="text/javascript">
function path_root() {
	return "<?php echo _PATH_ROOT_; ?>";
}

function path_ajax(file) {
	return "<?php echo _PATH_ROOT_; ?>ajax/"+file+".php";
}

//mantiene la sessione attiva (aggiorna ogni 5 minuti)
function tieniSessione() {
	$.ajax("<?php echo _PATH_ROOT_; ?>ajax/sess.php");
}
setInterval(tieniSessione,300000);

<?php 
$this->stampaCode('', '', 'js-code', 'stampaJs');
$this->stampaCode('$(document).ready(function () {', '});', 'js-load', 'stampaJsOnload');
?>

</script>
</head>

<body>
<div id="dialog-segnala" class="modal hide fade nostampa" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Segnalazione errori</h3>
	</div>
	<div class="modal-body"><form id="form-segnala">
		Email: 
		<input type="text" name="email" value="<?php echo Auth::getEmailSegnalazione(); ?>"><br>
		Segnalazione:<br>
		<textarea name="descrizione" class="input-block-level" id="segnala_msg" required="required"></textarea>
		<div id="segnalazione_obblig" class="alert">Inserire una descrizione dell'errore</div>
		<div id="segnalazione_ok" class="alert alert-success">Segnalazione inviata correttamente</div>
		<div id="segnalazione_no" class="alert alert-error">Errore durante l'invio della segnalazione, ritenta</div>
	</form></div>
	<div class="modal-footer">
		<button type="button" id="invia-segnalazione" class="btn btn-primary" onclick="inviaSegnalazione(); return false;">Invia</button>
		<button type="button" class="btn" data-dismiss="modal">Chiudi</button>
	</div>
</div>

<div class="container">
    <div class="navbar navbar-static-top ">
	    <div class="navbar-inner nostampa">
		    <ul class="nav" style="text-transform: uppercase;">
				<li><a href="javascript:mostraSegnalazione();">segnala errori</a></li>
				<li class="divider-vertical"></li>
   				<li><a href="/2013/">Sito</a></li>
   				<li class="divider-vertical"></li>
   				<li><a href="/gare/<?php echo $this->subdirGare(); ?>">Iscrizione gare</a></li>
   				<li class="divider-vertical"></li>
       			<li><a href="/2013/?page_id=696">Calendario</a></li>
       			<li class="divider-vertical"></li>
	    	</ul>
<?php if (Auth::getUtente() !== NULL) {?>
	    	<ul class="nav pull-right">
	    		<li><a href="<?php echo _PATH_ROOT_;?>logout.php">Logout</a></li>
	    	</ul>
<?php } //if utente != NULL ?>
	    </div>
    </div>
    
    <div class="barra-menu">
	    <div class="row">
		    <div class="span4">
		    	<img src="<?php echo _PATH_ROOT_; ?>img/logo.png">
		    </div>
		    <div class="span8 nostampa" style="margin-top:30px;"><ul class="nav nav-pills">
		<?php $this->stampaMenu(); ?>
			</ul><ul class="nav nav-pills">
		<?php $this->stampaSubMenu(); ?>
			</ul></div>
	    </div>
    </div>
    
<?php $this->stampaBody(); ?>
</div>
</body>
</html>

<?php 
		include_class('Log');
		Log::debug('pagina caricata',$_SERVER["REQUEST_URI"]);
	} //function stampa() 

}