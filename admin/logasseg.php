<?php
session_start();
require_once 'config.inc.php';

Auth::loginAsSegr();
go_home();
