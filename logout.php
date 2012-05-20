<?php
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
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<meta http-equiv="Refresh" content="5; URL=index.html">
<title>test page2</title>
</head>
<body>
<?php
print 'session_id='.session_id().'<P>';
?>
セッション切断<BR>
ログアウトしました<P>
<a href="login.php">ログイン</a>画面へ5秒後自動的に遷移します<br>
</body>
</html>
