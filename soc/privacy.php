<?php
session_start();
require_once 'config.inc.php';
check_get('magg');
check_get('anno');
include_controller('ModelloPrivacy');

$ctrl = new ModelloPrivacy(Auth::getUtente()->getIDSocieta(), ($_GET['magg'] != 0), $_GET['anno']);
$ctrl->stampa();