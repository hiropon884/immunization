<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();

$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode","clinic");
$smarty->assign("location","none");
$smarty->display("tpl/user_top.tpl");
?>
