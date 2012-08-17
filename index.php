<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(false);
//$smarty->display("tpl/top.tpl");
$smarty->display("tpl/template.tpl");
?>