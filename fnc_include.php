<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");

$o_smarty = new MySmarty();
$o_smarty->assign("data","�C���N���[�h�E�t�@�C���̃e�X�g");
$o_smarty->display("tpl/top.tpl");

?>