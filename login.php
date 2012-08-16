<?php
ini_set('display_errors', 1);
require_once("class/MySmarty.class.php");
require_once("class/DBAccessor.php");

$smarty = new MySmarty();

session_start(); 
if(isset($_POST["clinic_id"])){
  $clinic_id = $_POST["clinic_id"];
}
if(isset($_POST["password"])){
  $passwd = $_POST["password"];
}

// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["login"])) {

	$db = DBAccessor::getInstance();
    //SQLサーバーへ接続
    //$link = mysql_connect('localhost', 'root', 'admin');
    //$link = mysql_connect('localhost', 'db_user', '123456');
    //if (!$link) {
    //  die('接続失敗です。'.mysql_error());
    //}
    //print('<p>接続に成功しました。</p>');

    // MySQLに対する処理
    //// テーブルへ接続
    //$db_selected = mysql_select_db('immunization', $link);
    //if (!$db_selected){
    //  die('データベース選択失敗です。'.mysql_error());
   // }
    //print('<p>user_dbデータベースを選択しました。</p>');

    //// 文字コード設定
    //mysql_set_charset('utf8');

    //// クエリーの実行
    //$str = "SELECT * FROM clinic WHERE clinic_id = " . $clinic_id . " AND passwd = '". $passwd . "'";
 
 	$ret = $db->verifyUserAccount($clinic_id, $passwd);
 	
    //print $str."<P>";
    //$result = mysql_query($str);
    //if (!$result) {
    //   die('クエリーが失敗しました。'.mysql_error());
    //} else {
      
      ////// 結果の行数を得る
      //$num_rows = mysql_num_rows($result);
      /////  認証に成功すると$num_row==1
      //if($num_rows == 1){
	//while ($row = mysql_fetch_assoc($result)) {
	  //print('<p>');
	  //print('clinic_id='.$row['clinic_id']);
	  //print(',password='.$row['passwd']);
	  //print('</p>');
	//}
	if($ret == SUCCESS){
		// ログインが成功した証をセッションに保存
		$_SESSION["clinic_id"] = $_POST["clinic_id"];

		// 管理者専用画面へリダイレクト
		//$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/anq_result.php";
		$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/userTop.php";
		header("Location: {$login_url}");
	} else { 
		$error_message = "ユーザ名もしくはパスワードが違っています。";
    }
    
    // サーバー切断
    //$close_flag = mysql_close($link);
    
    //if ($close_flag){
     // print('<p>切断に成功しました。</p>');
    //}
    //}
}

print 'session_id='.session_id().'<P>';

if ($error_message) {
  print '<font color="red">'.$error_message.'</font>';
}
//if ($error_db) {
//  print $error_db;
//}

$smarty->display("tpl/login.tpl");
?>
