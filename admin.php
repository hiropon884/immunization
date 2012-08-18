<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start(); 

//$smarty->session_check();
$smarty->assign("menu_state","0");
$smarty->assign("menu_flag","1");
$smarty->assign("mode","admin");
$smarty->assign("location","none");
$smarty->display("tpl/admin.tpl");
?>
