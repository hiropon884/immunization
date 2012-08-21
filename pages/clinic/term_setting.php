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

if (isset($_POST["reset"])) {
	$id = "-1";
} else {
	$id = $clinic_id;
}

$tableItem = $db->getTermSetting($id);

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
}

$attrs = array('width' => '600');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);

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
