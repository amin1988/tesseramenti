<?php
session_start();
require_once 'config.inc.php';
include_class('DBKiller');

$ut = Auth::getUtente();
if($ut === NULL)
	go_login();
if($ut->getTipo() != 1)
	go_home();

if(isset($_GET['id_utente']))
{
	$id_utente = $_GET['id_utente'];
	DBKillerUtil::get()->eliminaUtente($id_utente);
	redirect('admin/utenti.php');
}

if(isset($_GET['id_societa']))
{
	$id_societa = $_GET['id_societa'];
	DBKillerUtil::get()->eliminaSocieta($id_societa);
	redirect('admin/societa.php');
}