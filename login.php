<?php
session_start(); 
$clinic_id = $_POST["clinic_id"];
$passwd = $_POST["password"];

// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["login"])) {
  //if ($clinic_id != "" && $passwd != ""){
    //SQLサーバーへ接続
    //$link = mysql_connect('localhost', 'root', 'admin');
    $link = mysql_connect('localhost', 'db_user', '123456');
    if (!$link) {
      die('接続失敗です。'.mysql_error());
    }
    print('<p>接続に成功しました。</p>');

    // MySQLに対する処理
    //// テーブルへ接続
    $db_selected = mysql_select_db('immunization', $link);
    if (!$db_selected){
      die('データベース選択失敗です。'.mysql_error());
    }
    print('<p>user_dbデータベースを選択しました。</p>');

    //// 文字コード設定
    mysql_set_charset('utf8');

    //// クエリーの実行
    $str = "SELECT * FROM clinic WHERE clinic_id = " . $clinic_id . " AND passwd = '". $passwd . "'";
 
    //print $str."<P>";
    $result = mysql_query($str);
    if (!$result) {
       die('クエリーが失敗しました。'.mysql_error());
    } else {
      
      ////// 結果の行数を得る
      $num_rows = mysql_num_rows($result);
      /////  認証に成功すると$num_row==1
      if($num_rows == 1){
	while ($row = mysql_fetch_assoc($result)) {
	  print('<p>');
	  print('clinic_id='.$row['clinic_id']);
	  print(',password='.$row['passwd']);
	  print('</p>');
	}
	
	// ログインが成功した証をセッションに保存
	$_SESSION["clinic_id"] = $_POST["clinic_id"];

	// 管理者専用画面へリダイレクト
	//$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/anq_result.php";
	$login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/userTop.php";
	header("Location: {$login_url}");
      } else { 
	$error_message = "ユーザ名もしくはパスワードが違っています。";
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

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>ログイン画面</title>
</head>
<body>

<?php
print 'session_id='.session_id().'<P>';

if ($error_message) {
  print '<font color="red">'.$error_message.'</font>';
}
if ($error_db) {
  print $error_db;
}
?>

<form action="login.php" method="POST">
病院ID：<input type="text" name="clinic_id" value="" /><br />
パスワード：<input type="password" name="password" value="" /><br />
<input type="submit" name="login" value="ログイン" />
</form>
<P>
<a href="admin.php">Back to Admin Page</a><P>
</body>
</html>
