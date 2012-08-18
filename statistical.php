<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(false);

session_start();

$smarty->session_check();
$smarty->assign("menu_state", "1");
$smarty->assign("menu_flag", "2");
$smarty->assign("mode","clinic");
$smarty->assign("location","statistical");
$smarty->display("tpl/statistical.tpl");

?>
