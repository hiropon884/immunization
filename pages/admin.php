<?php

require_once("../class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start(); 

//$smarty->session_check();
$smarty->assign("menu_is_available","true");
$smarty->assign("mode","admin");
$smarty->assign("location","none");

$smarty->display(TPL_BASE."admin.tpl");
?>
