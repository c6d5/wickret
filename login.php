<?php
require_once('./assets/php/Admin.php');

$adminPanel = new Admin('justmyname', 'qwerty123');
$adminPanel->handleRequest();
?>