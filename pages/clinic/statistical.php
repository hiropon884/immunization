<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(false);

session_start();

$smarty->session_check();
$smarty->assign("menu_is_available", "true");
$smarty->assign("mode","clinic");
$smarty->assign("location","statistical");
$smarty->display("tpl/statistical.tpl");

?>
