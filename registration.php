<?php
session_start(); 
// User Id
$userId = "";
// Password
$passwd = "";

// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["userId"])) {
  
    //SQLサーバーへ接続
    //$link = mysql_connect('localhost', 'root', 'admin');
    $link = mysql_connect('localhost', 'db_user', '123456');
    if (!$link) {
      die('接続失敗です。'.mysql_error());
    }
    print('<p>接続に成功しました。</p>');

    // MySQLに対する処理
    //// テーブルへ接続
    $db_selected = mysql_select_db('user_db', $link);
    if (!$db_selected){
      die('データベース選択失敗です。'.mysql_error());
    }
    print('<p>user_dbデータベースを選択しました。</p>');

    //// 文字コード設定
    mysql_set_charset('utf8');

    //// クエリーの実行
    //$str = "SELECT * FROM user WHERE userid = '$userid' AND passwd = '$passwd'";
    //print $str."<P>";
    $result = mysql_query("SELECT * FROM user WHERE userid = '$userid' AND passwd = '$passwd'");
    if (!$result) {
       die('クエリーが失敗しました。'.mysql_error());
    } else {
      
      ////// 結果の行数を得る
      $num_rows = mysql_num_rows($result);
      /////  認証に成功すると$num_row==1
      if($num_rows == 1){
	while ($row = mysql_fetch_assoc($result)) {
	 
	  //print('userid='.$row['userid']);
	  $userId = $row['userid'];
	  //print(',password='.$row['passwd']);
	  $passwd = $row['passwd'];
	}
	
      } else if($num_rows > 1){ 
	$error_message = "該当するデータが複数あります。";
      } else {
	$error_message = "該当するデータがありません。";
      }
    }
    // サーバー切断
    $close_flag = mysql_close($link);
    
    if ($close_flag){
      print('<p>切断に成功しました。</p>');
    }
    //}
}
?>