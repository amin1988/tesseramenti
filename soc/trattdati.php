<?php
session_start();
require_once 'config.inc.php';
include_controller('ModelloTrattamentoDati');

$ctrl = new ModelloTrattamentoDati(Auth::getUtente()->getIDSocieta());
$ctrl->stampa();