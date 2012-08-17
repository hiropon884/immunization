<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();

$smarty->session_check();
$smarty->assign("menu_state", "1");
$smarty->display("tpl/userTop.tpl");
?>
