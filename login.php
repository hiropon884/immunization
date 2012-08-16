<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");
require_once("class/DBAccessor.php");

$smarty = new MySmarty(true);

session_start(); 
if(isset($_POST["clinic_id"])){
  $clinic_id = $_POST["clinic_id"];
}
if(isset($_POST["password"])){
  $passwd = $_POST["password"];
}

// エラーメッセージを格納する変数を初期化
$msg= "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["login"])) {

	$db = $smarty->getDb();
 	$ret = $db->verifyUserAccount($clinic_id, $passwd);
 	
	if($ret == SUCCESS){
		// ログインが成功した証をセッションに保存
		$_SESSION["clinic_id"] = $_POST["clinic_id"];

		// 管理者専用画面へリダイレクト
		//$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/anq_result.php";
		$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/userTop.php";
		header("Location: {$login_url}");
	} else { 
		$msg = "ユーザ名もしくはパスワードが違っています。";
    }
    
    // サーバー切断
    //$close_flag = mysql_close($link);
    
    //if ($close_flag){
     // print('<p>切断に成功しました。</p>');
    //}
    //}
}
$smarty->assign("state",$msg);

print 'session_id='.session_id().'<P>';

//if ($error_message) {
//  print '<font color="red">'.$error_message.'</font>';
//}
//if ($error_db) {
//  print $error_db;
//}

$smarty->display("tpl/login.tpl");
?>
