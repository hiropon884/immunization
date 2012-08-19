{include file="tpl/parts/common_header.tpl" title="login form" refresh="false"}
<div class="layoutMainContent">
	<center>
{if $state != ""}
	<div class="errorMsg">{$state}</div>
{/if}
<form action="login.php" method="POST">
	病院ID：<input type="text" name="clinic_id" value="" /><br />
	パスワード：<input type="password" name="password" value="" /><br />
	<input type="submit" name="login" value="ログイン" />
</form>
	</center>
</div>

{include file="tpl/parts/common_footer.tpl"}
