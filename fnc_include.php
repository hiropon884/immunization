<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");

$o_smarty = new MySmarty();
$o_smarty->assign("data","インクルード・ファイルのテスト");
$o_smarty->display("tpl/top.tpl");

?>