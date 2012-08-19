<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(false);
$smarty->assign("menu_is_available","false");
$smarty->assign("mode","none");
$smarty->assign("location","none");
$smarty->display("tpl/top.tpl");

//$smarty->display("tpl/template2.tpl");
?>