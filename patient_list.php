<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Patient Data Dump</title>
</head>
<body>

<?php
require_once 'HTML/Table.php';

session_start(); 
// ログイン済みかどうかの変数チェックを行う
if (!isset($_SESSION["clinic_id"])) {

  // 変数に値がセットされていない場合は不正な処理と判断し、ログイン画面へリダイレクトさせる
  $no_login_url = "http://{$_SERVER["HTTP_HOST"]}/immunization/login.php";
  header("Location: {$no_login_url}");
  exit;
}

$patient_attribute = array("person_id", "clinic_id", "patient_id", 
			  "family_name", "family_name_yomi", "personal_name", 
			  "personal_name_yomi", "birthday", "zipcode",
			   "location1", "location2", "tel", "email");
$patient_caption = array("人ID","病院ID", "患者ID", "氏", "氏（読み）","名", 
			"名（読み）", "生年月日", "郵便番号", "住所１", "住所２",
			"電話番号", "メールアドレス");
$clinic_id = $_SESSION["clinic_id"];

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
  $result = mysql_query("SELECT * FROM person");
  if (!$result) {
     die('クエリーが失敗しました。'.mysql_error());
  } else {  
    ////// 結果の行数を得る
    $num_rows = mysql_num_rows($result);
    echo 'total user numbera = ' . $num_rows . '<p>';
    
    $tableData = array();
    
    while ($row = mysql_fetch_assoc($result)) {
      $tableItem = array();
      for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {	
	$tableItem[] = $row[$patient_attribute[$cnt]];
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
//$table->setAutoFill('n/a');

$tbl_cnt=0;

echo "<form action=\"patient_top.php\" method=\"POST\">";
	   
for ($nr = 0; $nr < count($tableData); $nr++) {
  //echo $tableData[$nr][1]."<P>";
  if($tableData[$nr][1] == $clinic_id){
    //$table->setHeaderContents($tbl_cnt+1, 1, $tableData[$nr][0]);
     $str = "<button type=\"submit\" name=\"patient_id\" value=\"" . $tableData[$nr][0] ."\">選択</button>";
     $table->setCellContents($tbl_cnt+1, 0, $str); 
    for ($i = 0; $i < count($tableData[$nr]); $i++) {
      //echo $tableData[$nr][$i]." <P>";
      if ('' != $tableData[$nr][$i]) {
	$table->setCellContents($tbl_cnt+1, $i+1, htmlspecialchars($tableData[$nr][$i], ENT_QUOTES, 'UTF-8')); 
      }
    }
    $tbl_cnt++;
  }
}

$altRow = array('bgcolor' => 'lightgray');
$table->altRowAttributes(1, null, $altRow);

for ($cnt = 0; $cnt < count($patient_caption); $cnt++) {
  $table->setHeaderContents(0, $cnt+1, $patient_caption[$cnt]);
}

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
$table->setColAttributes(0, $hrAttrs);

echo $table->toHtml();
echo "</form>";

?>

<P>
<a href="userTop.php">Back to UserTop</a><P>

</body>
</html>