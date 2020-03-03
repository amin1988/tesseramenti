<?php
session_start();
require_once 'config.inc.php';
include_view('FileIscrizioneAcsi','FileIscrizioneAcsiXls_2');

set_time_limit(300);

$view = new FileIscrizioneAcsiXls_2(in_rinnovo(), isset($_GET['n']), isset($_GET['w']));
$view->stampa();