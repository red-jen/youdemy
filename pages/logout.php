<?php
session_start();

include('../classes/user.inc.php');

$detroy = unserialize($_SESSION['user']);
$detroy->logout();
unset($detroy);
header('location: login.php');