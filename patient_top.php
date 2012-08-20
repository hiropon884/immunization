<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);
$smarty->assign("menu_is_available", "true");
$smarty->assign("mode", "patient");
$smarty->assign("location", "none");

session_start();

// ログイン済みかどうかの変数チェックを行う
$smarty->session_check();

$clinic_id = $_SESSION["clinic_id"];
if (!isset($_POST["person_id"])) {
	$person_id = $_SESSION["person_id"];
} else {
	$person_id = $_POST["person_id"];
}

$smarty->assign("clinic_id", $clinic_id);
$smarty->assign("person_id", $person_id);

try {
	$db = $smarty->getDb();
	$ret = $db->getPatientData($person_id);
} catch (PDOException $e) {
	echo $e->getMessage();
	die;
}

$msg = "OK";
if ($ret == FAILURE) {
	$msg = "NG";
} else {
	$person_name = $ret['family_name'] . " " . $ret['personal_name'];
	$_SESSION["person_id"] = $ret['person_id'];
	$_SESSION["birthday"] = $ret['birthday'];
	$_SESSION["person_name"] = $person_name;
	$smarty->assign("person_id", $ret['person_id']);
	$smarty->assign("birthday", $ret['birthday']);
	$smarty->assign("person_name", $person_name);
	$smarty->assign("menu_state", "2");
}
$smarty->assign("person_state", $msg);

$smarty->display("tpl/patient_top.tpl");
?>
