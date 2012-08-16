<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");

$smarty = new MySmarty(false);
$smarty->display("tpl/top.tpl");

?>