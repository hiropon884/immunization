<?php
session_start(); 
if (isset($_POST["userid"])) {
  $userid = $_POST["userid"];
}
if (isset($_POST["passwd"])) {
  $passwd = $_POST["passwd"];
}
$cmd = "";
if (isset($_POST["regist1"])) {
  echo "regist1<P>";
  $cmd = "regist1";
}
if (isset($_POST["regist2"])) {
  echo "regist2<P>";
  $cmd = "regist2";
}
if (isset($_POST["regist3"])) {
  echo "regist3<P>";
  $cmd = "regist3";
}
//var_dump($_POST);
// エラーメッセージを格納する変数を初期化
$error_message = "";

// ログインボタンが押されたかを判定
// 初めてのアクセスでは認証は行わずエラーメッセージは表示しないように
if (isset($_POST["userid"])) {
  
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

    //// 新規ユーザーの追加
    if($cmd == "regist1"){
      if(inputCheck($userid, $passwd) == true){
	////// 既存ユーザーとIDがかぶらないかチェックする
	$ret = userIdVerify($userid);
	if($ret == true){
	  print "<font color=\"red\">Error</font>: すでに同じユーザー名の登録があります。別のIDを入力してください<P>";
	} else {
	  $str = "INSERT INTO user VALUES ('" . $userid ."','" . $passwd ."');";
	  $result = mysql_query($str);
	  if (!$result) {
	    print "クエリーが失敗しました。".mysql_error()."<P>";
	    //die('クエリーが失敗しました。'.mysql_error());
	  } else {
	    print "<font color=\"red\">Success</font>: userid = '".$userid."'のデータを登録しました。<P>";
	  }
	} 
      }
    } else if($cmd == "regist2"){ // 既存ユーザーのデータ更新
      if(inputCheck($userid, $passwd) == true){
	$ret = userIdVerify($userid);
	if($ret == false){
	  print "<font color=\"red\">Error</font>: 入力されたユーザーIDを持つユーザーは存在しません。ユーザーIDを確認してください。<P>";
	} else {
	  if(passwordVerify($userid, $passwd) == true){
	    print "UPDATE"."<P>";
	  }
	}
      }
    } else if($cmd == "regist3"){ // ユーザーデータの削除
      if(inputCheck($userid, $passwd) == true){
	$ret = userIdVerify($userid);
	if($ret == false){
	  print "<font color=\"red\">Error</font>: 入力されたユーザーIDを持つユーザーは存在しません。ユーザーIDを確認してください。<P>";
	} else {
	  if(passwordVerify($userid, $passwd) == true){
	   
	    $result = mysql_query("DELETE FROM user WHERE userid = '$userid' AND passwd = '$passwd'");
	    if (!$result) {
	      //die('クエリーが失敗しました。'.mysql_error());
	      print "クエリーが失敗しました。".mysql_error()."<P>";
	    } else {
	      print "<font color=\"red\">Success</font>: userid = '".$userid."'のデータを削除しました。<P>";
	    }
	  }
	}
      }
    }
    
    //// 表の中身をダンプする
    //$str = "SELECT * FROM user WHERE userid = '$userid' AND passwd = '$passwd'";
    //print $str."<P>";
    $result = mysql_query("SELECT * FROM user");
    if (!$result) {
      die('クエリーが失敗しました。'.mysql_error());
    } else {  
      ////// 結果の行数を得る
      $num_rows = mysql_num_rows($result);
      echo 'total user numbera = ' . $num_rows . '<p>';
      
      while ($row = mysql_fetch_assoc($result)) {
	print('<p>');
	print('userid='.$row['userid']);
	print(',password='.$row['passwd']);
	print('</p>');
      }
    }
    
    // サーバー切断
    $close_flag = mysql_close($link);
    
    if ($close_flag){
      print('<p>切断に成功しました。</p>');
    }
    //}
}

function inputCheck($id, $pwd){
   if($id == ""){
     print "<font color=\"red\">Error</font>: ユーザー名を入力してください<P>";
     return false;
   } else if($pwd == ""){
     print "<font color=\"red\">Error</font>: パスワードを入力してください。<P>";
     return false;
   } 
   return true;
}

function userIdVerify($id){
  $result = mysql_query("SELECT * FROM user WHERE userid = '$id'");
  if (!$result) {
    //die('クエリーが失敗しました。'.mysql_error());
    print "クエリーが失敗しました。".mysql_error()."</P>";
    return false;
  } else {
    $num_rows = mysql_num_rows($result);
    if($num_rows == 0){
      return false;
    }
  }
  return true;
}

function passwordVerify($id, $pwd){
  $result = mysql_query("SELECT * FROM user WHERE userid = '$id' AND passwd = '$pwd'");
  if (!$result) {
    print "クエリーが失敗しました。".mysql_error()."</P>";
    return false;
  } else {
    $num_rows = mysql_num_rows($result);
    if($num_rows == 0){
      print "<font color=\"red\">Error</font>: パスワードが一致しません。<P>";
      return false;
    } 
  }
  return true;
}

?>

<html>
<head>
<title>Registration View</title>
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

<form action="registrationView.php" method="POST">
ユーザ名：<input type="text" name="userid" value="<?php echo $userid; ?>" /><br />
パスワード：<input type="password" name="passwd" value="" /><br />
<input type="submit" name="regist1" value="新規登録" />
<input type="submit" name="regist2" value="更新" />
<input type="submit" name="regist3" value="削除" />
</form>
</body>
</html>
