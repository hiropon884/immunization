<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Patient Top Page </title>
</head>
<body>

<?php

require_once 'HTML/Table.php';
require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start(); 

// ログイン済みかどうかの変数チェックを行う
$smarty->session_check();

$clinic_id = $_SESSION["clinic_id"];
if(!isset($_POST["person_id"])) {
  $person_id = $_SESSION["person_id"];
} else {
  $person_id = $_POST["person_id"];
}
//$birthday = "";
$smarty->assign("clinic_id", $clinic_id);
$smarty->assign("person_id", $person_id);
//echo "clinic_id = " . $clinic_id . "<P>";
//echo "person_id = " . $person_id . "<P>";
/*
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
  
}*/

$db = $smarty->getDb();
$ret = $db->getUserInfo($person_id);
$msg = "OK";
if($ret == FAILURE){
    $msg = "NG";
} else {
    $person_name = $ret['family_name'] . " " . $ret['personal_name'];
    $_SESSION["person_id"] = $ret['person_id'];
    $_SESSION["birthday"] = $ret['birthday'];
    $_SESSION["person_name"] = $person_name;
    $smarty->assign("person_id", $ret['person_id']);
    $smarty->assign("birthday", $ret['birthday']);
    $smarty->assign("person_name", $person_name);
    $smarty->assign("menu_state", $ret['2']);
}
$smarty->assign("person_state", $msg);
?>

<?php
/*
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
}*/
?>
