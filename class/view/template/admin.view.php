<?php
if (!defined("_BASE_DIR_")) exit();
include_view('template/base');

define('TEMPLATE_CLASS','TemplateAdmin');

define('SEZ_ADMIN_HOME','Home');
define('SEZ_ADMIN_GEST','Gestione');
define('SEZ_ADMIN_RICHIESTE','Richieste affiliazione');
define('SEZ_ADMIN_LOGAS','Login societ&agrave;');
define('SEZ_ADMIN_LOG','Log');

class TemplateAdmin extends TemplateBase {
	function stampaMenu() {
		?>
		<li <?php $this->menuClass(SEZ_ADMIN_HOME); ?>>
	    	<a href="<?php echo _PATH_ROOT_;?>admin/">Home</a>
	    </li>
	    <li <?php $this->menuClass(SEZ_ADMIN_GEST, "dropdown"); ?>>
	    	<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Gestione <b class="caret"></b></a>
	    	<ul class="dropdown-menu">
	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>admin/utenti.php">Utenti</a></li>
	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>admin/societa.php">Societ&agrave;</a></li>
	    	</ul>
	    </li>
		<li <?php $this->menuClass(SEZ_ADMIN_RICHIESTE); ?>>
	    	<a href="<?php echo _PATH_ROOT_;?>admin/listarichaff.php">Richieste affiliazioni</a>
	    </li>
	    <li <?php $this->menuClass(SEZ_ADMIN_LOGAS, "dropdown"); ?>>
	    	<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Login mascherato <b class="caret"></b></a>
	    	<ul class="dropdown-menu">
	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>admin/logasseg.php">Segreteria</a></li>
	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>admin/logas.php">Societ&agrave;</a></li>
	    	</ul>
	    </li>
	    <li <?php $this->menuClass(SEZ_ADMIN_LOG); ?>>
	    	<a href="<?php echo _PATH_ROOT_;?>admin/work/log.php">Log</a>
	    </li>
	    <?php 
	}
}