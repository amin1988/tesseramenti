<?php

if (!defined("_BASE_DIR_")) exit();

include_view('template/base');



define('TEMPLATE_CLASS','TemplateSegr');



define('SEZ_SEGR_HOME','Home');

define('SEZ_SEGR_SOC','Societ&agrave;');

define('SEZ_SEGR_PAGA','Pagamenti');

define('SEZ_SEGR_ACSI','Assicurazione');

define('SEZ_SEGR_RICHIESTE','Richieste affiliazione');

define('SEZ_SEGR_LOGAS','Login societ&agrave;');

define('SEZ_SEGR_VAR','Varie');



define('TMPL_ATTR_SUBSEZ','subsez');



define('SEZ_SEGR_SOC_DATI','Dati societ&agrave;');

define('SEZ_SEGR_SOC_TESS','Tesserati');

define('SEZ_SEGR_SOC_PAGA','Pagamenti');

// define('SEZ_SEGR_SOC_','');

// define('SEZ_SEGR_SOC_','');



class TemplateSegr extends TemplateBase {

		

	function stampaMenu() {

		?>

		<li <?php $this->menuClass(SEZ_SEGR_HOME); ?>>

	    	<a href="<?php echo _PATH_ROOT_;?>segr/">Home</a>

	    </li>

		<li <?php $this->menuClass(SEZ_SEGR_SOC, "dropdown"); ?>>

			<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Societ&agrave <b class="caret"></b></a>

	    	<ul class="dropdown-menu">

		    	<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/listasoc.php">F.I.A.M.</a></li>
			
			<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/listasockudo.php">Kudo</a></li>

		    	<!-- <li class="divider"></li> -->

		    	<!-- <li class="disabled"><a tabindex="-1" href="#">Altre federazioni</a></li> -->

		    	<!-- <li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/listasocfed.php">Altre federazioni</a></li>-->

	    	</ul>

	    		<!-- <a href="<?php echo _PATH_ROOT_;?>segr/listasoc.php">Societ&agrave;</a> -->

	    </li>

		<li <?php $this->menuClass(SEZ_SEGR_PAGA, "dropdown"); ?>>

	    	<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Pagamenti <b class="caret"></b></a>

	    	<ul class="dropdown-menu">

		    	<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/paga_lista.php">F.I.A.M.</a></li>

		    	<li class="divider"></li>

		    	<!--  <li class="disabled"><a tabindex="-1" href="#">Altre federazioni</a></li> -->

		    	<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/pagariepfed.php">Altre federazioni</a></li>

	    	</ul>

	    </li>

	    <li <?php $this->menuClass(SEZ_SEGR_ACSI, "dropdown"); ?>>

	    	<a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Assicurazione <b class="caret"></b></a>

	    	<ul class="dropdown-menu">

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/acsi/agg_tess.php">Aggiungi tessere</a></li>

	    		<li class="divider"></li>

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/acsi/invio.php">Invio tesseramenti</a></li>

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/acsi/conferma.php">Conferma tesseramenti</a></li>

	    		<li class="divider"></li>

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/acsi/gest_tess.php">Genera tesserini</a></li>

	    		<!--aggiunta conferma polizza -->
                        <li class="divider"></li>
                        <li><a tabindex="-1" href="<?php echo _PATH_ROOT_; ?>segr/acsi/conferma_poliz.php">Convalida polizze integrative</a></li>

	    	</ul>

	    </li>

	    <li <?php $this->menuClass(SEZ_SEGR_RICHIESTE); ?>>

	    	<a href="<?php echo _PATH_ROOT_;?>segr/listarichaff.php">Richieste affiliazioni</a>

	    </li>

	    <li <?php $this->menuClass(SEZ_SEGR_LOGAS); ?>>

	    	<a href="<?php echo _PATH_ROOT_;?>segr/logas.php">Login societ&agrave;</a>

	    </li>

	    <li <?php $this->menuClass(SEZ_SEGR_VAR, "dropdown"); ?>>

	    <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">Varie <b class="caret"></b></a>

	    	<ul class="dropdown-menu">

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>segr/comunicazione.php">Comunicazione</a></li>

	    		<li class="divider"></li>

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>segr/spostatess.php">Sposta tesserati</a></li>

	    		<li class="divider"></li>

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>segr/arbitri.php">Arbitri</a></li>

	    		<li class="divider"></li>

	    		<li><a tabindex="-1" href="<?php echo _PATH_ROOT_;?>segr/idonee.php">Societ&agrave Assemblea</a></li>

	    	</ul>

	    </li>

	    <?php 

	}

	

	protected function stampaSubMenu() {

		if ($this->getAttr(TMPL_ATTR_SUBSEZ) === NULL) return;

		

		include_model('Societa','Tipo');

		$soc = Societa::fromId($_GET['soc']);

		?>

		<li>

	    	<a href="<?php echo _PATH_ROOT_;?>segr/soc/dati.php?soc=<?php echo $_GET['soc']; ?>">

	    		Dati societ&agrave;

	    	</a>

	    </li>

	    <?php $this->menuTess($soc); ?>

    	<li>

    		<a href="<?php echo _PATH_ROOT_;?>segr/soc/pagamenti.php?soc=<?php echo $_GET['soc']; ?>">

    			Pagamenti

    		</a>

    	</li>

    	<li class="pull-right">

	    	<a href="<?php echo _PATH_ROOT_;?>segr/listasoc.php">

	    		Cambia societ&agrave;

	    	</a>

	    </li>

    	<li class="disabled pull-right">

	    	<a href="#"><?php echo $soc->getNome(); ?></a>

	    </li>

    	<?php 

	}

	

	private function menuTess($soc) {

		$sett = $soc->getIDSettori();

		$cs = count($sett);

		echo '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#">';

		echo 'Tesserati <b class="caret"></b></a><ul class="dropdown-menu">';

		if ($cs == 1) {

			reset($sett);

			$this->menuSettore(current($sett));

		} elseif ($cs > 1) {

			foreach ($sett as $idsett) {

				$s = Settore::fromId($idsett);

				echo '<li class="dropdown-submenu">';

				echo '<a tabindex="-1" href="#">'.$s->getNome().'</a>';

				echo '<ul class="dropdown-menu">';

				$this->menuSettore($idsett);

				echo '</ul></li>';

			}

		}

		if ($cs > 0)

			echo '<li class="divider"></li>';

		echo '<li><a tabindex="-1" href="'._PATH_ROOT_.'segr/soc/altritess.php?soc='.$_GET['soc'].'">Altri tesserati</a></li>';

		echo '<li><a tabindex="-1" href="'._PATH_ROOT_.'segr/soc/nuovo.php?soc='.$_GET['soc'].'">Nuovo tesserato</a></li>';

		echo "</ul></li>\n";

	}

	

	private function menuSettore($idsett) {

		$tl = Tipo::getFromSettore($idsett);

		foreach (Tipo::getFromSettore($idsett) as $idt => $tipo) {

			$nome = $tipo->getPlurale();

			echo "<li><a tabindex=\"-1\" href=\""._PATH_ROOT_."segr/soc/tesslist.php?soc=$_GET[soc]&idt=$idt\">$nome</a></li>";

		}

	}

}