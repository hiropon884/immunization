<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start(); 

//$smarty->session_check();
$smarty->assign("menu_state","0");
$smarty->display("tpl/userTop.tpl");
?>
