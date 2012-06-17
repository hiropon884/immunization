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

$clinic_id = $_SESSION["clinic_id"];
$term_attr = array("clinic_id","immunization_id","times","term_start","term_end");
$term_caption = array("病院ID","予防接種名","回数","推奨接種時期","接種時期");
$term_vars = array();
$medicine = array("インフルエンザb型(ヒブ)","肺炎球菌(PCV7)",
		  "B型肝炎(HBV)","ロタウイルス","三種混合(DPT)",
		  "BCG","ポリオ","麻しん、風しん(MR)","水痘",
		  "おたふくかぜ", "日本脳炎", "インフルエンザ",
		  "2種混合(DT)",
		  "ヒトパピローマウイルス(HPV) - 2価ワクチン",
		  "ヒトパピローマウイルス(HPV) - 4価ワクチン",
		  "A型肝炎");

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

if($_POST["reset"]){
  $str = "SELECT * FROM immunization_term WHERE clinic_id = '-1';";
} else {
  echo "clinic_id = " . $clinic_id ."<P>";
  $str = "SELECT * FROM immunization_term WHERE clinic_id = '" . $clinic_id . "';";
}
echo $str . "<P>";
$result = mysql_query($str);
if (!$result) {
  die('クエリーが失敗しました。'.mysql_error());
} else {  
  
  ////// 結果の行数を得る
  $num_rows = mysql_num_rows($result);
  echo 'total record number = ' . $num_rows . '<p>';
  
  $tableItem = array();
  while ($row = mysql_fetch_assoc($result)) {
    $item = array();
    for ($cnt = 0; $cnt < count($term_attr); $cnt++) {
      $item[] = $row[$term_attr[$cnt]];
    }
    $tableItem[] = $item;
  }
}	    
//echo "test=".$tableItem[1][3] . "<BR>";
//echo "test=".$tableItem[2][3] . "<BR>";
//echo "test=".$tableItem[3][3] . "<BR>";
//echo "test=".$tableItem[4][3] . "<BR>";
if($_POST["update"]){
  $str = "SELECT immunization_id, frequency FROM immunization;";
  $result = mysql_query($str);
  $num_rows = mysql_num_rows($result);
  $nc = 0;
  while ($row = mysql_fetch_assoc($result)) {
    //echo $row['immunization_id'] . "_" . $row['frequency'] . "<P>";
    //$str = $row['immunization_id'] . "_" . $row['frequency'];
    //echo $_POST[$str];
    for($i=1;$i<=$row['frequency'];$i++){
      $id = $row['immunization_id'];
      $key = $id . "_" . $i;
      $val = $_POST[$key];
      //echo "test (" . $tableItem[$nc][3] . ", " . $val . ", " . $key . ")<P>";
      if($tableItem[$nc][3] != $val){
	
	$str = "UPDATE immunization_term SET term_start = '" . $val
	  . "' WHERE clinic_id = '" . $clinic_id . "' AND immunization_id = '"
	  . $tableItem[$nc][1]. "' AND times = '" . $tableItem[$nc][2] . "';";
	echo $str . "<P>";
	$result = mysql_query($str);
	if (!$result) {
	  print "クエリーが失敗しました。".mysql_error()."<P>";
	}
	$tableItem[$nc][3] = $val;
      }
      $nc++;
    }
  }
}

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

//$table->setCellContents(0, 0, "");
//$table->setCellContents(0, 1, "値");
for($i=1;$i<count($term_attr)-1;$i++){
  $table->setCellContents(0, $i-1, $term_caption[$i]);
}

for ($j = 0; $j < count($tableItem); $j++) {
  $item = $tableItem[$j];
  $last = count($term_attr)-1;
  for($i=1;$i<$last;$i++){
    if($i == 1){
      if($item[$i+1] == 1){
	$table->setCellContents($j+1, $i-1, $medicine[$item[$i]-1]);
      }
    } else if($i == $last-1){
      $id = $item[1] . "_" . $item[2];
      $str = "生後 <input type='text' name='" . $id . "' value='" . $item[$i] . "' size=5 /> ヶ月目";
      $table->setCellContents($j+1, $i-1, $str);
    } else if($i == 2){
      $str = $item[$i] . "回目";
      $table->setCellContents($j+1, $i-1, $str);
    } else {
      $table->setCellContents($j+1, $i-1, $item[$i]);
    }
    if(($item[1])%2 == 1){
      $hrAttrs = array('bgcolor' => 'WhiteSmoke');
    } else {
      $hrAttrs = array('bgcolor' => 'GhostWhite');
    }
    $table->setRowAttributes($j+1, $hrAttrs, true);
  }
}

//$altRow = array('bgcolor' => 'lightgray');
//$table->altRowAttributes(0, null, $altRow);

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Clinic Setting</title>
</head>
<body>
<?php
print 'session_id='.session_id().'<P>';
?>
<?php
echo "<form action=\"immunization_term_setting.php\" method=\"POST\">";
echo $table->toHtml();
echo "<input type=\"submit\" name=\"update\" value=\"更新\" />";
echo "<input type=\"submit\" name=\"reset\" value=\"初期化\" />";
echo "</form>";
?>
<P>
<a href="userTop.php">Back to User Top Page</a><br>

</body>
</html>
