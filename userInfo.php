<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>All User Dump</title>
</head>
<body>

<?php
require_once 'HTML/Table.php';
$clinic_attribute = array("clinic_id", "passwd", "name", "yomi", "zipcode",
			  "location1", "location2", "tel", "email");
$clinic_caption =array("病院ID", "パスワード", "病院名", "病院名（読み）",
		       "郵便番号", "住所１", "住所２",
		       "電話番号", "メールアドレス", );

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
  print('<p>immunization データベースを選択しました。</p>');

  //// 文字コード設定
  mysql_set_charset('utf8');

  //// クエリーの実行
  //$str = "SELECT * FROM user WHERE userid = '$userid' AND passwd = '$passwd'";
  //print $str."<P>";
  $result = mysql_query("SELECT * FROM clinic");
  if (!$result) {
     die('クエリーが失敗しました。'.mysql_error());
  } else {  
    ////// 結果の行数を得る
    $num_rows = mysql_num_rows($result);
    echo 'total user numbera = ' . $num_rows . '<p>';
    
    $tableData = array();
    
    while ($row = mysql_fetch_assoc($result)) {
      $tableItem = array();
      for ($cnt = 0; $cnt < count($clinic_attribute); $cnt++) {
	$tableItem[] = $row[$clinic_attribute[$cnt]];
      }
     
      $tableData[] = $tableItem;
    }
  }
// サーバー切断
$close_flag = mysql_close($link);

// create html table with all user information
$attrs = array('width' => '800');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);
$table->setAutoFill('n/a');

for ($nr = 0; $nr < count($tableData); $nr++) {
  $table->setHeaderContents($nr+1, 0, $tableData[$nr][0]);
  for ($i = 1; $i < count($tableData[$nr]); $i++) {
    if ('' != $tableData[$nr][$i]) {
      $table->setCellContents($nr+1, $i, htmlspecialchars($tableData[$nr][$i], ENT_QUOTES, 'UTF-8'));
    }
  }
  if($nr%2 == 1){
    $hrAttrs = array('bgcolor' => 'WhiteSmoke');
  } else {
    $hrAttrs = array('bgcolor' => 'GhostWhite');
  }
  $table->setRowAttributes($nr+1, $hrAttrs, true);
}
//$altRow = array('bgcolor' => 'lightgray');
//$table->altRowAttributes(1, null, $altRow);

for ($cnt = 0; $cnt < count($clinic_caption); $cnt++) {
  $table->setHeaderContents(0, $cnt, $clinic_caption[$cnt]);
}

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
//$table->setColAttributes(0, $hrAttrs);

echo $table->toHtml();

?>

<P>
<a href="admin.php">Back</a><P>

</body>
</html>