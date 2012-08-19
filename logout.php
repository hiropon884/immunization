<?php

require_once("class/MySmarty.class.php");

$smarty = new MySmarty(true);
$smarty->assign("menu_is_available", "false");
$smarty->assign("mode","none");
$smarty->assign("location","none");

//明示的にDBの接続を閉じる
$db = $smarty->getDb();
$db = null;

// セッションの初期化
// session_name("something")を使用している場合は特にこれを忘れないように!
session_start();

// セッション変数を全て解除する
$_SESSION = array();

// セッションを切断するにはセッションクッキーも削除する。
// Note: セッション情報だけでなくセッションを破壊する。
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 最終的に、セッションを破壊する
session_destroy();

$smarty->display("tpl/logout.tpl");
?>
