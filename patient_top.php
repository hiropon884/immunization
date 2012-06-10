<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Patient Top Page </title>
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
/*
$patient_attribute = array("person_id", "clinic_id", "patient_id", 
			  "family_name", "family_name_yomi", "personal_name", 
			  "personal_name_yomi", "birthday", "zipcode",
			   "location1", "location2", "tel", "email");
$patient_caption = array("人ID","病院ID", "患者ID", "氏", "氏（読み）","名", 
			"名（読み）", "生年月日", "郵便番号", "住所１", "住所２",
			"電話番号", "メールアドレス");
$patient_vars_min = array(1, 1, 1, 1, 1, 1, 1, 10, 8, 1, 1, 12 ,1);
$patient_vars_max = array(10, 10, 20, 10, 20, 10, 20, 10, 8, 255, 255, 13, 50);

$patient_vars = array();
$table_error = array();
$verify = false;
*/
/*
$posted_item_num=0;
for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {
  $table_error[] = false;
  //$patient_vars[$cnt] = "null";
  if(isset($_POST[$patient_attribute[$cnt]])){
    $posted_item_num++;
    $patient_vars[$cnt] = $_POST[$patient_attribute[$cnt]];
  }
  }*/
//print_r($patient_vars);

$clinic_id = $_SESSION["clinic_id"];
if(!isset($_POST["person_id"])) {
  $person_id = $_SESSION["person_id"];
} else {
  $person_id = $_POST["person_id"];
}
$birthday = "";
echo "clinic_id = " . $clinic_id . "<P>";
echo "person_id = " . $person_id . "<P>";

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
$str = "SELECT * FROM person WHERE person_id = $person_id";
//print $str."<P>";
$result = mysql_query($str);
if (!$result) {
  die('クエリーが失敗しました。'.mysql_error());
} else {  
  ////// 結果の行数を得る
  $num_rows = mysql_num_rows($result);
  echo 'total user number = ' . $num_rows . '<p>';
  if($num_rows > 1){
    echo "ERROR: Many patient was detected.<P>";
  }
  $row = mysql_fetch_assoc($result);
  $birthday = $row['birthday'];
  $family_name = $row['family_name'];
  $personal_name = $row['personal_name'];
  echo "birthday=".$birthday."<P>";
  //while ($row = mysql_fetch_assoc($result)) {
  //  $tableItem = array();
  //for ($cnt = 0; $cnt < count($patient_attribute); $cnt++) {	
  //print $row[$patient_attribute[$cnt]] . "<P>";
  //}
  
  //$tableData[] = $tableItem;
  
}

// サーバー切断
$close_flag = mysql_close($link);

$_SESSION["person_id"] = $person_id;
$_SESSION["birthday"] = $birthday;
$person_name = $family_name . " " . $personal_name;
$_SESSION["person_name"] = $person_name;
echo "氏名：" . $person_name . "<BR>";
echo "生年月日："  . $birthday ."<P>";
?>
<a href="appointment.php">個別予防接種予約</a><BR>
<a href="patient_calendar.php">予防接種カレンダー</a><BR>
<a href="patient_past.php">接種履歴詳細</a><BR>
<a href="patient_booklist.php">予約一覧表示</a><P>
推奨予防接種（2ヶ月分）<P>

<H3>カレンダーから予防接種予約を入れる</H3>
<?php

$attrs = array('width' => '400');
$table = new HTML_Table($attrs);

$now_year = date("Y"); // 現在の年を取得
$now_month = date("n"); // 在の月を取得
$now_day = date("j"); // 現在の日を取得

$table->setCellContents(0, 0, make_calendar($now_year, $now_month, $now_day, 1));

if($now_month < 12){
  $now_month++;
} else {
  $now_year++;
  $now_month = 1;
}
$now_day = 1;
$fir_weekday = date( "w", mktime( 0, 0, 0, $now_month, 1, $now_year ) );

//make_calendar($now_year, $now_month, $now_day, 0);
$table->setCellContents(0, 1, make_calendar($now_year, $now_month, $now_day, 0));

echo $table->toHtml();

function make_calendar($year, $month, $day, $flag){
  $html = "";
  $weekday = array( "日", "月", "火", "水", "木", "金", "土" );
  // 1日の曜日を数値で取得
  $fir_weekday = date( "w", mktime( 0, 0, 0, $month, 1, $year ) );
  
  $html .= '<table border="1" cellspacing="0" cellpadding="5" style="text-align:center;">';
  // 見出し部分<caption>タグ出力
  $html .= "<caption style=\"color:black; font-size:14px; padding:0px;\">"
    .$year."年".$month."月のカレンダー</caption>\n";
  
  $html .=  "<tr>\n";
  
  // 曜日セル<th>タグ設定
  $i = 0; // カウント値リセット
  while( $i <= 6 ){ // 曜日分ループ
    
    //-------------スタイルシート設定---------------------------------
    if( $i == 0 ){ // 日曜日の文字色
      $style = "#C30";
    }
    else if( $i == 6 ){ // 土曜日の文字色
      $style = "#03C";
    }
    else{ // 月曜～金曜日の文字色
      $style = "black";
    }
    //-------------スタイルシート設定終わり---------------------------
    
    // <th>タグにスタイルシートを挿入して出力
    $html .= "\t<th style=\"color:".$style."\">".$weekday[$i]."</th>\n";
    $i++; //カウント値+1
  }

  // 行の変更
  $html .= "</tr>\n";
  $html .= "<tr>\n";
  
  $i = 0; //カウント値リセット（曜日カウンター）
  while( $i != $fir_weekday ){ //１日の曜日まで空白（&nbsp;）で埋める
    $html .= "\t<td>&nbsp;</td>\n";
    $i++;
  }
 
  // 今月の日付が存在している間ループする
  for( $cnt=1; checkdate( $month, $cnt, $year ); $cnt++ ){
    
    //曜日の最後まできたらカウント値（曜日カウンター）を戻して行を変える
    if( $i > 6 ){
      $i = 0;
      $html .= "</tr>\n";
      $html .= "<tr>\n";
    }
    
    //-------------スタイルシート設定-----------------------------------
    if( $i == 0 ){ //日曜日の文字色
      $style = "#C30";
    }
    else if( $i == 6 ){ //土曜日の文字色
      $style = "#03C";
    }
    else{ //月曜～金曜日の文字色
      $style = "black";
    }
    
    // 今日の日付の場合、背景色追加
    if( $cnt == $day && $flag == 1){
      $style = $style."; background:silver";
    }
    //-------------スタイルシート設定終わり-----------------------------
    
    // 日付セル作成とスタイルシートの挿入
    $html .= "\t<td style=\"color:".$style.";\">".$cnt."</td>\n";
    
    $i++; //カウント値（曜日カウンター）+1
  }
  
  while( $i < 7 ){ //残りの曜日分空白（&nbsp;）で埋める
    $html .= "\t<td>&nbsp;</td>\n";
    $i++;
  }
  $html .= "</tr>\n";
  $html .= "</table>\n";
  return $html;
}
?>
<P>
<a href="userTop.php">Back to User Top Page</a><P>
</form>
</body>
</html>
