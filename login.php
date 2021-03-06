<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);
$smarty->assign("menu_is_available", "false");
$smarty->assign("mode", "none");
$smarty->assign("location", "none");

session_start();
if (isset($_POST["clinic_id"])) {
	$clinic_id = $_POST["clinic_id"];
}
if (isset($_POST["password"])) {
	$passwd = $_POST["password"];
}

// エラーメッセージを格納する変数を初期化
$msg = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["login"])) {
	try {
		$db = $smarty->getDb();
		$ret = $db->verifyClinicIDwithPW($clinic_id, $passwd);
	} catch (PDOException $e) {
		echo $e->getMessage();
		die;
	}
	if ($ret == SUCCESS) {
		// ログインが成功した証をセッションに保存
		$_SESSION["clinic_id"] = $_POST["clinic_id"];

		// 管理者専用画面へリダイレクト
		//$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/anq_result.php";
		$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/pages/user_top.php";
		header("Location: {$login_url}");
	} else {
		$msg = "ユーザ名もしくはパスワードが違っています。";
	}
}
$smarty->assign("state", $msg);


//print 'session_id=' . session_id() . '<P>';

$smarty->display(TPL_BASE."login.tpl");
?>
