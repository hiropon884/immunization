{include file="tpl/common_header.tpl" title="login form"}

<form action="login.php" method="POST">
病院ID：<input type="text" name="clinic_id" value="" /><br />
パスワード：<input type="password" name="password" value="" /><br />
<input type="submit" name="login" value="ログイン" />
</form>
<P>
<a href="index.php">Back to Top Page</a><P>

{include file="tpl/common_footer.tpl"}
