<?php
if (!defined("_BASE_DIR_")) exit();
include_view('template/base');

define('TEMPLATE_CLASS','TemplateDefault');

class TemplateDefault extends TemplateBase {
	function stampaMenu() {
		echo '<li><a href="'.go_home(true).'">Home</a></li>';
	}
}