<?php
require_once("../../class/MySmarty.class.php");

$smarty = new MySmarty(true);

session_start();
$smarty->session_check();

$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "clinic");
$smarty->assign("location", "setting");

$db = $smarty->getDb();
$clinic_id = $_SESSION["clinic_id"];
//echo $clinic_id;
$params['term'] = $smarty->getTermParams();
$term_attr = $params['term']['attribute'];
$term_caption = $params['term']['caption'];
//$term_vars = array();
$params['medicine'] = $smarty->getMedicineName();
$medicine = $params['medicine']['caption'];
/*
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
 */
if (isset($_POST["reset"])) {
	//$str = "SELECT * FROM immunization_term WHERE clinic_id = '-1';";
	$id = "-1";
} else {
	//echo "clinic_id = " . $clinic_id ."<P>";
	//$str = "SELECT * FROM immunization_term WHERE clinic_id = '" . $clinic_id . "';";
	$id = $clinic_id;
}

$tableItem = $db->getTermSetting($id);

/*
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
  } */
//echo "test=".$tableItem[1][3] . "<BR>";
//echo "test=".$tableItem[2][3] . "<BR>";
//echo "test=".$tableItem[3][3] . "<BR>";
//echo "test=".$tableItem[4][3] . "<BR>";
if (isset($_POST["update"])) {
	try {
		$rows = $db->getImmunizationIDList();
	} catch (PDOException $e) {
		echo $e->getMessage();
		die;
	}
	$nc = 0;
	$enable_flag = 0;
	foreach ($rows as $item) {
		for ($i = 1; $i <= $item['frequency']; $i++) {
			$id = $item['immunization_id'];
			$key = "start_" . $id . "_" . $i;
			$enable = "enable_" . $id . "_" . $i;
			$val = $_POST[$key];
			if ($i == 1) {
				if (isset($_POST[$enable])) {
					$enable_flag = 1;
				} else {
					$enable_flag = 0;
				}
			}
			if ($tableItem[$nc][3] != $val || $tableItem[$nc][5] != $enable_flag) {
				try {
					$db->updateTermSetting($val, $enable_flag, $clinic_id, $tableItem[$nc][1], $tableItem[$nc][2]);
				} catch (PDOException $e) {
					echo $e->getMessage();
					die;
				}
				$tableItem[$nc][3] = $val;
				$tableItem[$nc][5] = $enable_flag;
			}
			$nc++;
		}
	}
	//$str = "SELECT immunization_id, frequency FROM immunization;";
	//$result = mysql_query($str);
	//$num_rows = mysql_num_rows($result);
	/*
	$nc = 0;
	$enable_flag = 0;
	while ($row = mysql_fetch_assoc($result)) {
		//echo $row['immunization_id'] . "_" . $row['frequency'] . "<P>";
		//$str = $row['immunization_id'] . "_" . $row['frequency'];
		//echo $_POST[$str];
		for ($i = 1; $i <= $row['frequency']; $i++) {
			$id = $row['immunization_id'];
			$key = "start_" . $id . "_" . $i;
			$enable = "enable_" . $id . "_" . $i;
			$val = $_POST[$key];
			if ($i == 1) {
				if (isset($_POST[$enable])) {
					$enable_flag = 1;
				} else {
					$enable_flag = 0;
				}
			}
			//var_dump($enable_flag);
			//echo "test (" . $tableItem[$nc][3] . ", " . $val . ", " . $key . ")<P>";
			if ($tableItem[$nc][4] != $val || $tableItem[$nc][1] != $enable_flag) {

				$str = "UPDATE immunization_term SET term_start = '" . $val
						. "', is_enable = " . $enable_flag
						. " WHERE clinic_id = '" . $clinic_id . "' AND immunization_id = '"
						. $tableItem[$nc][2] . "' AND times = '" . $tableItem[$nc][3] . "';";
				//echo $str . "<P>";
				$result_update = mysql_query($str);
				if (!$result_update) {
					print "クエリーが失敗しました。" . mysql_error() . "<P>";
				}
				$tableItem[$nc][4] = $val;
				$tableItem[$nc][1] = $enable_flag;
			}
			$nc++;
		}
	}
	 * 
	 */
}
//print_r($tableItem);
$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

//$table->setCellContents(0, 0, "");
//$table->setCellContents(0, 1, "値");
for ($i = 1; $i < count($term_attr) - 2; $i++) {
	$table->setCellContents(0, $i, $term_caption[$i]);
}
$table->setCellContents(0, 0, $term_caption[5]);

for ($j = 0; $j < count($tableItem); $j++) {
	$item = $tableItem[$j];
	//print_r($item);
	$last = count($term_attr);
	$id = $item[1] . "_" . $item[2];
	for ($i = 1; $i < $last; $i++) {
		if($i==4){
			continue;
		}
		if ($term_attr[$i] == "immunization_id") {
			if ($item[$i + 1] == 1) {
				$str = $medicine[$item[$i] - 1];
				$table->setCellContents($j + 1, $i, $str);
			}
		} else if ($term_attr[$i] == "term_start") {
			//$id = $item[1] . "_" . $item[2];
			//echo "aaa = ". $item[$i] ." i=".$i."<P>";
			$str = "生後 <input type='text' name='start_" . $id . "' value='" . $item[$i] . "' size=5 /> ヶ月目";
			$table->setCellContents($j + 1, $i, $str);
		} else if ($term_attr[$i] == "times") {
			$str = $item[$i] . "回目";
			$table->setCellContents($j + 1, $i, $str);
		} else if ($term_attr[$i] == "is_enable") {
			//$id = $item[1] . "_" . $item[2];
			if ($item[2] == 1) {
				$checkbox = "";
				if ($item[$i] == "1") {
					$checkbox = "checked";
				}
				$str = "<input type='checkbox' name='enable_" . $id . "' value='" . $item[$i] . "' " . $checkbox . "/>";
				$table->setCellContents($j + 1,  0, $str);
			}
		} else {
			$table->setCellContents($j + 1, $i, $item[$i]);
		}
		if (($item[1]) % 2 == 1) {
			$hrAttrs = array('bgcolor' => 'WhiteSmoke');
		} else {
			$hrAttrs = array('bgcolor' => 'GhostWhite');
		}
		$table->setRowAttributes($j + 1, $hrAttrs, true);
	}
}

//$altRow = array('bgcolor' => 'lightgray');
//$table->altRowAttributes(0, null, $altRow);

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
$smarty->assign("table", $table->toHtml());

$smarty->display(TPL_BASE . "term_setting.tpl");
?>
