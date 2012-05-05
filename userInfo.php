<?php

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

?>