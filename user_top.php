<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();

$smarty->session_check();
$smarty->assign("menu_state", "1");
$smarty->assign("menu_flag", "1");
$smarty->display("tpl/user_top.tpl");
?>