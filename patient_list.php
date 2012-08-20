<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);
$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "clinic");
$smarty->assign("location", "patient_list");

session_start();
$smarty->session_check();

$params = $smarty->getPatientParams();

$patient_caption = $params['patient']['caption'];

$clinic_id = $_SESSION["clinic_id"];

$db = $smarty->getDb();
try {
	$tableData = $db->getPatinetList($clinic_id);
} catch (PDOException $e) {
	echo $e->getMessage();
	die;
}

// create html table with all user information
$attrs = array('width' => '800');
$table = new HTML_Table($attrs);
$table->setAutoGrow(true);
//$table->setAutoFill('n/a');

$tbl_cnt = 0;

for ($nr = 0; $nr < count($tableData); $nr++) {
	//echo $tableData[$nr][1]."<P>";
	if ($tableData[$nr][1] == $clinic_id) {
		//$table->setHeaderContents($tbl_cnt+1, 1, $tableData[$nr][0]);
		$str = "<button type=\"submit\" name=\"person_id\" value=\"" . $tableData[$nr][0] . "\">選択</button>";
		$table->setCellContents($tbl_cnt + 1, 0, $str);
		for ($i = 0; $i < count($patient_caption); $i++) {
			//echo $tableData[$nr][$i]." <P>";
			if ('' != $tableData[$nr][$i]) {
				$table->setCellContents($tbl_cnt + 1, $i + 1, htmlspecialchars($tableData[$nr][$i], ENT_QUOTES, 'UTF-8'));
			}
		}
		$tbl_cnt++;
	}
	if ($nr % 2 == 1) {
		$hrAttrs = array('bgcolor' => 'WhiteSmoke');
	} else {
		$hrAttrs = array('bgcolor' => 'GhostWhite');
	}
	$table->setRowAttributes($nr + 1, $hrAttrs, true);
}

//$altRow = array('bgcolor' => 'lightgray');
//$table->altRowAttributes(1, null, $altRow);

for ($cnt = 0; $cnt < count($patient_caption); $cnt++) {
	$table->setHeaderContents(0, $cnt + 1, $patient_caption[$cnt]);
}

$hrAttrs = array('bgcolor' => 'silver');
$table->setRowAttributes(0, $hrAttrs, true);
$table->setColAttributes(0, $hrAttrs);

$smarty->assign("table", $table->toHtml());
$smarty->display("tpl/patient_list.tpl");
?>
